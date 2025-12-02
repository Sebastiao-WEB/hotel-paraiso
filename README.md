# 🏨 Sistema de Gestão de Reservas Hoteleiras - Hotel Paraíso

Sistema completo de gestão de reservas desenvolvido com **Laravel 10** e **Livewire 3**.

## 📋 Requisitos

- PHP >= 8.1
- Composer
- MySQL/MariaDB
- Node.js e NPM

## 🚀 Instalação

1. **Clone o repositório e instale as dependências:**

```bash
composer install
npm install
```

2. **Configure o arquivo `.env`:**

```bash
cp .env.example .env
php artisan key:generate
```

Configure as credenciais do banco de dados no arquivo `.env`:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=hotel_paraiso
DB_USERNAME=seu_usuario
DB_PASSWORD=sua_senha
```

3. **Execute as migrations e seeders:**

```bash
php artisan migrate --seed
```

4. **Compile os assets:**

```bash
npm run build
# ou para desenvolvimento:
npm run dev
```

5. **Inicie o servidor:**

```bash
php artisan serve
```

6. **Configure o scheduler (em produção):**

Adicione ao crontab:

```bash
* * * * * cd /caminho/do/projeto && php artisan schedule:run >> /dev/null 2>&1
```

Para desenvolvimento, execute:

```bash
php artisan schedule:work
```

## 👤 Usuários Padrão

Após executar os seeders, você pode fazer login com:

- **Administrador:**
  - Email: `admin@hotelparaiso.com`
  - Senha: `password`

- **Recepcionista:**
  - Email: `recepcionista@hotelparaiso.com`
  - Senha: `password`

- **Limpeza:**
  - Email: `limpeza@hotelparaiso.com`
  - Senha: `password`

## 📦 Funcionalidades

### ✅ RF01: Gestão de Clientes
- CRUD completo de clientes (pessoas físicas e empresas)
- Filtros por nome, tipo e telefone

### ✅ RF02: Consulta de Disponibilidade
- Consulta de quartos disponíveis por tipo e datas
- Validação automática de conflitos de reserva

### ✅ RF03: Gestão de Reservas
- Criação de reservas com validação de disponibilidade
- Formulário completo com busca de cliente e quarto

### ✅ RF04: Confirmação de Reservas
- Confirmação manual de reservas
- Atualização automática do estado do quarto

### ✅ RF05: Cancelamento de Reservas
- Cancelamento manual
- Cancelamento automático (no-show às 14h via scheduler)

### ✅ RF06: Check-in
- Realização de check-in com registro do funcionário
- Atualização automática do estado do quarto

### ✅ RF07: Check-out
- Cálculo automático de cobrança (diárias + serviços)
- Registro de forma de pagamento

### ✅ RF08: Notas de Cobrança
- Geração automática de notas para empresas
- Exportação em PDF

### ✅ RF09: Gestão de Limpeza
- Marcação de quartos "em limpeza"
- Marcação de quartos como "disponível" após limpeza

### ✅ RF10: Serviços Extras
- CRUD de serviços (restaurante, lavanderia, etc.)
- Adição de serviços a reservas em check-in

## 🎯 Estrutura do Projeto

```
app/
├── Console/
│   ├── Commands/
│   │   └── CancelarReservasNoShow.php
│   └── Kernel.php
├── Http/
│   └── Controllers/
│       └── NotaCobrancaController.php
├── Livewire/
│   ├── Dashboard.php
│   ├── Clientes/
│   ├── Quartos/
│   ├── Reservas/
│   ├── Checkin/
│   ├── Servicos/
│   ├── Faturacao/
│   └── Limpeza/
└── Models/
    ├── Cliente.php
    ├── Quarto.php
    ├── Reserva.php
    ├── ServicoExtra.php
    ├── ReservaServico.php
    ├── NotaCobranca.php
    └── User.php
```

## 🔧 Comandos Artisan

### Cancelar Reservas No-Show
```bash
php artisan reservas:cancelar-no-show
```

Este comando é executado automaticamente às 14h diariamente via scheduler.

### Executar Scheduler (Desenvolvimento)
```bash
php artisan schedule:work
```

## 📊 Dashboard

O dashboard exibe:
- Estatísticas gerais (clientes, quartos, reservas, receita)
- Gráfico de ocupação (últimos 7 dias)
- Próximos check-ins e check-outs

## 🎨 Tecnologias Utilizadas

- **Laravel 10** - Framework PHP
- **Livewire 3** - Componentes reativos
- **TailwindCSS** - Estilização
- **Laravel Breeze** - Autenticação
- **Spatie Activitylog** - Auditoria
- **DomPDF** - Geração de PDFs
- **MySQL** - Banco de dados

## 📝 Notas

- O sistema utiliza **Spatie Activitylog** para auditoria de todas as operações
- As reservas são automaticamente canceladas às 14h se não confirmadas (no-show)
- Notas de cobrança são geradas automaticamente para empresas no check-out
- O estado dos quartos é atualizado automaticamente conforme o fluxo de reservas

## 🐛 Troubleshooting

### Erro ao gerar PDF
Certifique-se de que o DomPDF está instalado:
```bash
composer require barryvdh/laravel-dompdf
```

### Erro de permissões
```bash
chmod -R 775 storage bootstrap/cache
```

### Limpar cache
```bash
php artisan config:clear
php artisan cache:clear
php artisan view:clear
```

## 📄 Licença

Este projeto é de código aberto e está disponível sob a licença MIT.
# hotel
# hotel
# hotel
