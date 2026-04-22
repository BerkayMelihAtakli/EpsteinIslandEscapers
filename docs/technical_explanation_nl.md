# EpsteinIslandEscapers - Technische Documentatie

## 1. Projectoverzicht
EpsteinIslandEscapers is een PHP-gebaseerd web escape room-spel met:
- Teamaanmaak en sessietracking
- Drie spelkamers met puzzelprogressie
- Reviewsysteem (speler + admin)
- Adminpaneel met authenticatie en CRUD-achtige handelingen
- MySQL-opslag met PDO

Belangrijkste stack:
- Backend: PHP (procedurele stijl met helperfuncties)
- Database: MySQL via PDO
- Frontend: HTML, CSS, JavaScript (vanilla)

## 2. Runtime-architectuur
### 2.1 Requestflow
1. De browser vraagt een PHP-route op (bijvoorbeeld `index.php` of `rooms/room_2.php`).
2. Gedeelde includes laden de paginastructuur (`includes/header.php`, `includes/nav.php`, `includes/footer.php`).
3. De database-bootstrap laadt vanuit `database.php` en de schema-helper vanuit `includes/schema.php`.
4. De businesslogica draait (teamaanmaak, puzzelcontroles, reviewbewerkingen, adminoverzichten).
5. De output wordt server-side gerenderd.

### 2.2 Statusmodel
- Session (`$_SESSION`) bewaart de actieve teamidentiteit en spelvoortgang.
- De database bewaart persistente entiteiten: teams, vragen en reviews.

## 3. Databaselaag
### 3.1 Verbinding
Bestand: `database.php`
- Maakt de database aan als die ontbreekt (`epsteinislandescapers`)
- Opent PDO met UTF-8 (`utf8mb4`)
- Zet exceptions aan via `PDO::ATTR_ERRMODE`

### 3.2 Schemainitialisatie
Bestand: `includes/schema.php`
- Functie `ensureProjectSchema(?PDO $db_connection): void`
- Zorgt dat tabellen bestaan:
  - `teams`
  - `question`
  - `reviews`
- Seedt en migreert puzzelrecords (inclusief normalisatie/vertaalgedrag)

### 3.3 Gebruik van PDO
Database read/write-operaties zijn in de hele applicatie met PDO geïmplementeerd:
- `prepare(...)` + `execute(...)` voor geparameteriseerde writes/reads
- `query(...)` voor directe lijstoverzichten waar geen gebruikersinvoer wordt geïnjecteerd

## 4. Domeinentiteiten
### 4.1 Team
Tabel: `teams`
- Kernvelden: `team_name`, `member1..member4`
- Trackingvelden: `score`, `created_at`, `finished_at`, `elapsed_seconds`

### 4.2 Puzzel
Tabel: `question`
- `riddle`, `answer`, `hint`, `roomId`

### 4.3 Review
Tabel: `reviews`
- `team_id`, `rating`, `difficulty`, `feedback`, `created_at`
- `team_id` FK verwijst naar `teams.id` (`ON DELETE SET NULL`)

## 5. Gameplaymodules
### 5.1 Teamaanmaak
Bestand: `create_team.php`
- Valideert minimale verplichte velden
- Voegt een teamrecord toe
- Slaat teamcontext op in de sessie (`team_id`, `team_name`)
- Geeft JSON terug voor de AJAX-modalflow

### 5.2 Kamer 1
Bestanden:
- `rooms/room_1.php`
- `js/room1-scene.js`
- `rooms/complete_room1.php`

Doel:
- Behandelt de eerste gameplayfase en de voortgang naar volgende kamers

### 5.3 Kamer 2
Bestand: `rooms/room_2.php`
- Laadt kamerpuzzels uit de database (`roomId = 2`)
- Past helpers voor antwoordnormalisatie toe
- Sluit de meta exit-trial-riddle uit van de lockerlijst

### 5.4 Kamer 3
Bestand: `rooms/room_3.php`
- Laadt de laatste 3 room-3-riddles uit de database (`roomId = 3`)
- Valideert antwoorden en houdt de huidige index in de sessie bij
- Roept `finalizeTeamEscape(...)` aan bij voltooiing
- Slaat `finished_at` en `elapsed_seconds` op

## 6. Reviewsysteem
### 6.1 Publieke reviewinzending
Bestanden:
- `submit_review.php`
- `reviews.php`

Gedrag:
- Dwingt een completion-gate af voordat een review mag worden geplaatst
- Accepteert rating/difficulty/feedback
- Slaat review op met eventueel gekoppeld team

### 6.2 Review-UI
Bestanden:
- `includes/footer.php`
- `js/review-modal.js`

Gedrag:
- Toont recente reviews in een modal
- Verwerkt AJAX-submissie en responsestaten

## 7. Adminpaneel
### 7.1 Authenticatie
Bestanden:
- `admin/auth.php`
- `admin/login.php`
- `admin/logout.php`

Gedrag:
- Sessie-gebaseerde login-guard (`adminRequireLogin()`)
- Redirect naar login als de gebruiker niet is geauthenticeerd

### 7.2 Teamadmin
Bestanden:
- `admin/add_team.php`
- `admin/show_all_teams.php`

Gedrag:
- Teamrecords aanmaken
- Volledig overzicht lezen inclusief scores, verstreken tijd, reviewaantal en gemiddelde rating

### 7.3 Riddle-admin
Bestanden:
- `admin/add_riddle.php`
- `admin/show_all_riddles.php`

Gedrag:
- Puzzels met antwoord/hint aanmaken
- Alle puzzels tonen
- Puzzelrecords verwijderen

### 7.4 Review-admin
Bestanden:
- `admin/add_review.php`
- `admin/show_all_reviews.php`

Gedrag:
- Handmatig reviews aanmaken
- Alle reviews tonen
- Reviewrecords verwijderen

## 8. Gedeelde UI-componenten
- `includes/header.php`: globale `<head>`-resources
- `includes/nav.php`: bovenste navigatie + team-badge
- `includes/footer.php`: footer + create-team/review-modals + JS-koppeling
- `css/style.css`: hoofdthema en adminstyling

## 9. JavaScript-modules
- `js/menu.js`: gedrag van burger-/zijmenu
- `js/join-riddle.js`: vloeiende sectienavigatie voor de cult-anchor en cult-unlockflow
- `js/create-team-modal.js`: modal open/close/toggle + AJAX-teamaanmaak
- `js/review-modal.js`: levenscyclus van modal + AJAX-reviewinzending
- `js/room1-scene.js`: logica voor de puzzelscène van kamer 1

## 10. Beveiligings- en data-integriteitsnotities
- SQL-injectionrisico verminderd via PDO prepared statements in writeflows
- Adminroutes beschermd door login-guard
- Sessies dragen spelstatus en teamidentiteit
- Mogelijke toekomstige hardening:
  - CSRF-tokens voor destructieve adminacties
  - Wachtwoordhashing / credentials via environment variables

## 11. Naleving van maporganisatie
De huidige structuur is gescheiden op verantwoordelijkheid:
- `admin/` voor admin-endpoints/views
- `rooms/` voor gameplay
- `includes/` voor gedeelde partials/helpers
- `js/` voor JavaScript-modules
- `css/` voor styles
- `assets/` voor statische media/fonts
- `sql/` voor SQL-seed/referentie

## 12. Complete bestandskaart (huidig project)
### Root
- `index.php`
- `create_team.php`
- `database.php`
- `reviews.php`
- `submit_review.php`
- `unlock_cult.php`
- `README.md`

### Admin
- `admin/index.php`
- `admin/auth.php`
- `admin/login.php`
- `admin/logout.php`
- `admin/add_team.php`
- `admin/show_all_teams.php`
- `admin/add_riddle.php`
- `admin/show_all_riddles.php`
- `admin/add_review.php`
- `admin/show_all_reviews.php`

### Includes
- `includes/header.php`
- `includes/nav.php`
- `includes/footer.php`
- `includes/schema.php`

### Rooms
- `rooms/room_1.php`
- `rooms/room_2.php`
- `rooms/room_3.php`
- `rooms/complete_room1.php`

### Frontend-assets
- `css/style.css`
- `js/create-team-modal.js`
- `js/join-riddle.js`
- `js/menu.js`
- `js/review-modal.js`
- `js/room1-scene.js`

### Overig
- `sql/riddles.sql`
- `assets/*` (afbeeldingen, iconen, fonts)

## 13. Uitgevoerde opschoning
De codebase is opgeschoond om bestanden te verwijderen die niet langer door de draaiende site werden gebruikt:
- legacy geëxporteerd puzzelbestand verwijderd
- ongebruikte oude win/verlies-standalonepagina's verwijderd
- niet-gerefereerde JavaScript-bestanden verwijderd
- externe fallback-afhankelijkheid van Room 2 vervangen door een inline fallback-array

## 14. Technische eindsamenvatting
De codebase levert een volledig speelbare web escape room met persistente teamdata, puzzelbeheer, reviewbeheer en adminoverzicht, georganiseerd in aparte mappen en aangedreven door PDO-gebaseerde MySQL-integratie.
