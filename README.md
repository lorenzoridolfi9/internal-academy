# Internal Academy 🎓

Piattaforma per la gestione di workshop aziendali interni. Permette agli Admin di creare e gestire workshop, e ai dipendenti di iscriversi e gestire le proprie partecipazioni.

## Stack Tecnologico

- **Backend:** Laravel 13 (PHP 8.5)
- **Frontend:** Vue 3 + Inertia.js + TypeScript + Tailwind CSS
- **Database:** MySQL 8
- **Testing:** Pest

## Funzionalità

### Must Have

- ✅ Autenticazione con due ruoli: Admin e Employee
- ✅ CRUD workshop per gli Admin
- ✅ Dashboard employee con lista workshop futuri
- ✅ Iscrizione e cancellazione workshop con un click

### Show off

- ✅ Waiting list con logica FIFO — se un confermato si cancella, il primo in lista d'attesa viene promosso automaticamente
- ✅ Controllo sovrapposizioni orarie — impossibile iscriversi a due workshop che si sovrappongono
- ✅ Comando Artisan `academy:remind` per inviare email di reminder ai partecipanti del giorno successivo

### Top Player

- ✅ Dashboard statistiche Admin (workshop più popolare, totale iscrizioni, dipendenti)
- ✅ Aggiornamento real-time del contatore iscrizioni tramite polling
- ✅ 51 test con Pest (Unit + Feature)

## Requisiti

- PHP 8.5+
- Composer
- Node.js 22+
- Docker (per il database MySQL)

## Installazione

### 1. Clona il repository

```bash
git clone https://github.com/lorenzoridolfi9/internal-academy.git
cd internal-academy
```

### 2. Installa le dipendenze PHP

```bash
composer install
```

### 3. Installa le dipendenze Node

```bash
npm install
```

### 4. Configura l'ambiente

```bash
cp .env.example .env
php artisan key:generate
```

### 5. Configura il database

Aggiorna il file `.env` con le credenziali del database:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=internal_academy
DB_USERNAME=academy
DB_PASSWORD=academy
```

### 6. Avvia il database con Docker

```bash
docker compose up -d db
```

### 7. Esegui le migration

```bash
php artisan migrate
```

### 8. Avvia l'applicazione

In due terminali separati:

```bash
# Terminale 1 — Backend
php artisan serve

# Terminale 2 — Frontend
npm run dev
```

L'applicazione è disponibile su `http://localhost:8000`.

## Dati di Test

Per popolare il database con dati di esempio:

```bash
php artisan migrate:fresh --seed
```

Questo crea:

- **1 Admin:** `admin@academy.it` / `password`
- **10 Employee:** password `password` per tutti (email generate casualmente tramite Faker)
- **6 Workshop:** 5 normali + 1 pieno con waiting list

## Eseguire i Test

```bash
php artisan test
```

Per eseguire solo i test unitari:

```bash
php artisan test --testsuite=Unit
```

Per eseguire solo i feature test:

```bash
php artisan test --testsuite=Feature
```

## Comando Reminder Email

Per inviare email di promemoria a tutti i partecipanti dei workshop del giorno successivo:

```bash
php artisan academy:remind
```

In sviluppo le email vengono scritte nel log (`storage/logs/laravel.log`). Per mandare email reali configura le variabili SMTP nel `.env`:

```env
MAIL_MAILER=smtp
MAIL_HOST=sandbox.smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=il_tuo_username
MAIL_PASSWORD=la_tua_password
MAIL_FROM_ADDRESS="academy@internal.it"
MAIL_FROM_NAME="Internal Academy"
```

## Decisioni Architetturali

### Pattern Controller → Service → Model

I controller sono snelli e delegano tutta la logica di business ai Service (`WorkshopService`, `RegistrationService`). Questo rende il codice testabile, riutilizzabile e manutenibile.

### Inertia.js invece di API REST separata

L'applicazione è full-stack e non necessita di API pubbliche. Inertia elimina la duplicazione di routing e semplifica la gestione dello stato.

### Waiting List FIFO

La promozione dalla waiting list avviene dentro una `DB::transaction()` per garantire atomicità — cancellazione e promozione avvengono sempre insieme, il DB non rimane mai in uno stato inconsistente.

### Polling

Il contatore iscrizioni sulla dashboard admin si aggiorna ogni 10 secondi tramite polling. È una soluzione semplice ed efficace per questo caso d'uso. Per scenari con molti utenti simultanei si potrebbe migrare a WebSocket con Laravel Reverb.

### TypeScript invece di JavaScript

Tutto il frontend è scritto in TypeScript per garantire type safety, ridurre gli errori a runtime e migliorare la manutenibilità del codice.
