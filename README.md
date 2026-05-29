# DSAuctions 🚗🔨

Questo repository contiene il codice sorgente completo per **DSAuctions**, una web application per la gestione di aste online di automobili, ispirata a piattaforme reali come AutoBidMaster. Il progetto è stato sviluppato come **Capolavoro** per la piattaforma Unica.

L'applicazione è stata realizzata nell'ambito dell'Anno Scolastico 2025-2026 presso l'I.S. "L. da Vinci - Fascetti" (Classe 5INF/B) da **Diyar Soken**.

---

## 🚀 Panoramica del Progetto

L'obiettivo del progetto è realizzare una piattaforma funzionale per la compravendita di veicoli tramite aste digitali. Il sistema garantisce trasparenza e facilità d'uso, automatizzando la gestione dei rilanci e delle scadenze temporali.

### Funzionalità Principali
* **Catalogo Virtuale:** Visualizzazione dei veicoli disponibili con dettagli tecnici e prezzi correnti.
* **Sistema di Aste Progressive:** Gestione delle offerte in tempo reale con aggiornamento automatico del prezzo.
* **Gestione Ruoli:** Distinzione gerarchica tra Visitatori, Offerenti e Amministratori.
* **Business Logic:** Controllo rigoroso sui vincoli di integrità (es. offerte sempre superiori al prezzo attuale, gestione scadenze).
* **Integrità Dati:** Ogni automobile è associata a un'unica asta (rapporto 1:1).

## 🛠️ Tecnologie Utilizzate

### Backend
* **Linguaggio:** PHP
* **Database:** SQL (MySQL/MariaDB)
* **Logica:** Architettura relazionale con gestione di vincoli e generalizzazioni (Utente/Offerente/Amministratore).

### Frontend
* **Linguaggi:** HTML5, CSS3, JavaScript
* **Interfaccia:** Design focalizzato sulla leggibilità del catalogo e dei dettagli dell'asta.

---

## ⚙️ Come Avviare il Progetto

Segui questi passaggi per configurare ed eseguire il progetto localmente tramite un ambiente LAMP/WAMP/XAMPP.

### Prerequisiti
* [XAMPP](https://www.apachefriends.org/) o un server Apache/PHP equivalente.
* [MySQL Server](https://www.mysql.com/).
* Un client Git installato.

### 1. Clonazione del Repository
Clona il repository nella cartella pubblica del tuo server locale (es. `htdocs` per XAMPP):

```bash
git clone [https://github.com/tuo-username/DSAuctions.git](https://github.com/tuo-username/DSAuctions.git)
cd DSAuctions
