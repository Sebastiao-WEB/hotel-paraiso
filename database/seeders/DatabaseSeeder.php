<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Cliente;
use App\Models\Quarto;
use App\Models\ServicoExtra;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Usuários
        User::create([
            'name' => 'Administrador',
            'email' => 'admin@hotelparaiso.com',
            'password' => Hash::make('password'),
            'cargo' => 'admin',
        ]);

        User::create([
            'name' => 'Maria Silva',
            'email' => 'recepcionista@hotelparaiso.com',
            'password' => Hash::make('password'),
            'cargo' => 'recepcionista',
        ]);

        User::create([
            'name' => 'João Santos',
            'email' => 'limpeza@hotelparaiso.com',
            'password' => Hash::make('password'),
            'cargo' => 'limpeza',
        ]);

        // Clientes
        Cliente::create([
            'nome' => 'João da Silva',
            'tipo' => 'pessoa',
            'email' => 'joao@email.com',
            'telefone' => '(11) 98765-4321',
            'nif' => '123.456.789-00',
            'endereco' => 'Rua das Flores, 123 - São Paulo, SP',
        ]);

        Cliente::create([
            'nome' => 'Maria Oliveira',
            'tipo' => 'pessoa',
            'email' => 'maria@email.com',
            'telefone' => '(11) 91234-5678',
            'nif' => '987.654.321-00',
            'endereco' => 'Av. Paulista, 1000 - São Paulo, SP',
        ]);

        Cliente::create([
            'nome' => 'Tech Solutions Ltda',
            'tipo' => 'empresa',
            'email' => 'contato@techsolutions.com',
            'telefone' => '(11) 3456-7890',
            'nif' => '12.345.678/0001-90',
            'endereco' => 'Av. Faria Lima, 2000 - São Paulo, SP',
        ]);

        Cliente::create([
            'nome' => 'Global Corp',
            'tipo' => 'empresa',
            'email' => 'financeiro@globalcorp.com',
            'telefone' => '(11) 3456-7891',
            'nif' => '98.765.432/0001-10',
            'endereco' => 'Rua Augusta, 500 - São Paulo, SP',
        ]);

        // Quartos
        $tipos = ['Standard', 'Superior', 'Deluxe', 'Suíte'];
        $estados = ['disponivel', 'disponivel', 'disponivel', 'ocupado', 'limpeza'];
        
        for ($i = 1; $i <= 20; $i++) {
            Quarto::create([
                'numero' => str_pad($i, 3, '0', STR_PAD_LEFT),
                'tipo' => $tipos[array_rand($tipos)],
                'preco_diaria' => rand(150, 500),
                'estado' => $estados[array_rand($estados)],
            ]);
        }

        // Serviços Extras
        ServicoExtra::create([
            'nome' => 'Restaurante',
            'preco' => 50.00,
        ]);

        ServicoExtra::create([
            'nome' => 'Lavanderia',
            'preco' => 30.00,
        ]);

        ServicoExtra::create([
            'nome' => 'Minibar',
            'preco' => 25.00,
        ]);

        ServicoExtra::create([
            'nome' => 'Room Service',
            'preco' => 40.00,
        ]);

        ServicoExtra::create([
            'nome' => 'Estacionamento',
            'preco' => 20.00,
        ]);

        $this->command->info('Seeders executados com sucesso!');
        $this->command->info('Usuários criados:');
        $this->command->info('  - admin@hotelparaiso.com / password (Admin)');
        $this->command->info('  - recepcionista@hotelparaiso.com / password (Recepcionista)');
        $this->command->info('  - limpeza@hotelparaiso.com / password (Limpeza)');
    }
}
