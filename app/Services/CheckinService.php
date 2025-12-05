<?php

namespace App\Services;

use App\Models\Stay;
use App\Models\Quarto;
use App\Models\Cliente;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use Exception;

/**
 * Service para gerenciar lógica de negócio de check-ins diretos (walk-in)
 * 
 * Encapsula todas as regras de negócio relacionadas a estadias diretas,
 * incluindo validações de disponibilidade, criação de estadias e check-out.
 */
class CheckinService
{
    /**
     * Verifica se um quarto está disponível no momento exato
     * 
     * @param int $roomId ID do quarto
     * @param Carbon|null $checkInAt Data/hora do check-in (null = agora)
     * @return bool
     */
    public function isRoomAvailable(int $roomId, ?Carbon $checkInAt = null): bool
    {
        $room = Quarto::findOrFail($roomId);
        $checkInAt = $checkInAt ?? now();

        // Verifica o estado do quarto
        if ($room->isOcupado()) {
            return false;
        }

        // Verifica se há estadias ativas no quarto
        $activeStay = Stay::where('room_id', $roomId)
            ->where('status', 'active')
            ->where('check_in_at', '<=', $checkInAt)
            ->where('expected_check_out_at', '>', $checkInAt)
            ->exists();

        if ($activeStay) {
            return false;
        }

        // Verifica se há reservas em check-in no quarto
        $activeReservation = \App\Models\Reserva::where('quarto_id', $roomId)
            ->where('status', 'checkin')
            ->where('data_entrada', '<=', $checkInAt->toDateString())
            ->where('data_saida', '>', $checkInAt->toDateString())
            ->exists();

        return !$activeReservation;
    }

    /**
     * Cria uma nova estadia direta (walk-in check-in)
     * 
     * @param array $data Dados da estadia
     * @return Stay
     * @throws Exception Se o quarto não estiver disponível
     */
    public function createWalkInCheckin(array $data): Stay
    {
        DB::beginTransaction();

        try {
            // Valida disponibilidade do quarto
            $checkInAt = isset($data['check_in_at']) 
                ? Carbon::parse($data['check_in_at']) 
                : now();

            if (!$this->isRoomAvailable($data['room_id'], $checkInAt)) {
                throw new Exception('O quarto selecionado não está disponível no momento.');
            }

            // Cria a estadia
            $stay = Stay::create([
                'guest_id' => $data['guest_id'],
                'room_id' => $data['room_id'],
                'check_in_at' => $checkInAt,
                'expected_check_out_at' => Carbon::parse($data['expected_check_out_at']),
                'status' => 'active',
                'created_by' => auth()->id(),
                'total_amount' => 0, // Será calculado no check-out
                'notes' => $data['notes'] ?? null,
            ]);

            // Atualiza o estado do quarto para ocupado
            $room = Quarto::findOrFail($data['room_id']);
            $room->update(['estado' => 'ocupado']);

            DB::commit();

            // Dispara evento de check-in criado
            event(new \App\Events\CheckinCreated($stay));

            return $stay;
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Erro ao criar check-in direto: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Realiza check-out de uma estadia
     * 
     * @param int $stayId ID da estadia
     * @param array $data Dados do check-out (payment_type, etc)
     * @return Stay
     * @throws Exception Se a estadia não estiver ativa
     */
    public function checkout(int $stayId, array $data = []): Stay
    {
        DB::beginTransaction();

        try {
            $stay = Stay::with('room')->findOrFail($stayId);

            if (!$stay->isActive()) {
                throw new Exception('Apenas estadias ativas podem fazer check-out.');
            }

            $checkOutAt = now();

            // Calcula o valor total
            $totalAmount = $stay->calculateTotalAmount();

            // Atualiza a estadia
            $stay->update([
                'actual_check_out_at' => $checkOutAt,
                'status' => 'completed',
                'checked_out_by' => auth()->id(),
                'total_amount' => $totalAmount,
                'payment_type' => $data['payment_type'] ?? null,
            ]);

            // Libera o quarto para limpeza
            $stay->room->update(['estado' => 'limpeza']);

            DB::commit();

            return $stay;
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Erro ao realizar check-out: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Cancela uma estadia ativa
     * 
     * @param int $stayId ID da estadia
     * @return Stay
     * @throws Exception Se a estadia não estiver ativa
     */
    public function cancel(int $stayId): Stay
    {
        DB::beginTransaction();

        try {
            $stay = Stay::with('room')->findOrFail($stayId);

            if (!$stay->isActive()) {
                throw new Exception('Apenas estadias ativas podem ser canceladas.');
            }

            // Atualiza a estadia
            $stay->update([
                'status' => 'cancelled',
            ]);

            // Libera o quarto
            $stay->room->update(['estado' => 'disponivel']);

            DB::commit();

            return $stay;
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Erro ao cancelar estadia: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Busca quartos disponíveis no momento
     * 
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getAvailableRooms()
    {
        return Quarto::whereIn('estado', ['disponivel', 'limpeza'])
            ->get()
            ->filter(function ($room) {
                return $this->isRoomAvailable($room->id);
            })
            ->values();
    }
}

