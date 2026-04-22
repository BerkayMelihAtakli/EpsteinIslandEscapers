# EpsteinIslandEscapers - Eenvoudige Uitleg

## Wat deze website doet
Dit is een escape room-website waar spelers:
1. Een team aanmaken
2. Door 3 kamers met puzzels spelen
3. Ontsnappen en een eindtijd krijgen
4. Een review achterlaten

Er is ook een adminomgeving om teams, riddles en reviews te beheren.

## Hoe het spel werkt (simpel)
### Stap 1: Een team aanmaken
- Een team vult een formulier in (teamnaam + leden).
- De site slaat het team op in de database.
- De teamnaam wordt in de sessie opgeslagen en in de navigatie getoond.

### Stap 2: Speel Room 1, Room 2, Room 3
- Elke kamer heeft eigen puzzellogica.
- Antwoorden worden door de site gecontroleerd.
- Het spel bewaart de voortgang in de sessie.

### Stap 3: Ontsnappen en tijd opslaan
- Wanneer het team Room 3 voltooit, slaat de site op:
  - Eindtijd
  - Totale verstreken tijd

### Stap 4: Review
- Teams kunnen na afloop een review inzenden.
- Reviews worden opgeslagen en op de site getoond.

## Wat de admin kan doen
### Teams
- Teams toevoegen
- Alle teams bekijken
- Score, eindtijd en reviewstatistieken bekijken

### Riddles
- Riddles, antwoorden en hints toevoegen
- Alle riddles bekijken
- Riddles verwijderen

### Reviews
- Reviews toevoegen
- Alle reviews bekijken
- Reviews verwijderen

## Belangrijkste mappen (eenvoudig overzicht)
- `admin/` -> adminpagina's
- `rooms/` -> spelkamers
- `includes/` -> gedeelde onderdelen zoals header/nav/footer
- `js/` -> JavaScript-gedrag
- `css/` -> styling
- `assets/` -> afbeeldingen en fonts
- `sql/` -> SQL-bestand

## Database in eenvoudige woorden
De website gebruikt MySQL met PDO. Belangrijkste tabellen:
- `teams` -> teaminformatie en timing
- `question` -> riddles en antwoorden
- `reviews` -> feedback van spelers

## Belangrijke bestanden (korte kaart)
- `index.php` -> homepagina
- `create_team.php` -> endpoint voor teamaanmaak
- `database.php` -> databaseverbinding
- `rooms/room_1.php` -> room 1
- `rooms/room_2.php` -> room 2
- `rooms/room_3.php` -> room 3 + logica voor eindtijd
- `submit_review.php` -> endpoint voor reviewinzending
- `reviews.php` -> publieke reviewpagina

## Korte technische kwaliteitssamenvatting
- Gebruikt PDO voor databasequeries
- Heeft sessie-gebaseerde spelstatus
- Gebruikt aparte mappen per verantwoordelijkheid
- Heeft admin-authenticatiepagina's

## Samenvatting in één zin
Het is een compleet PHP escape room-project met teambeheer, puzzelgameplay, tijdsopslag, reviews en een adminpaneel.
