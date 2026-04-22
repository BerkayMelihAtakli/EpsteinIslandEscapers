# EpsteinIslandEscapers

EpsteinIslandEscapers is a PHP escape room website with:
- team creation
- three playable rooms
- finish-time tracking
- player reviews
- an authenticated admin panel

## Current Project Structure

### Root
- `index.php` - home page
- `create_team.php` - team creation endpoint used by the modal
- `database.php` - PDO database connection and auto-create logic
- `reviews.php` - public reviews page
- `submit_review.php` - AJAX review submission endpoint
- `unlock_cult.php` - ritual gate unlock endpoint for the home-page riddle

### Shared Includes
- `includes/header.php` - global head resources
- `includes/nav.php` - global navigation and team badge
- `includes/footer.php` - footer, create-team modal, reviews modal, script loading
- `includes/schema.php` - schema bootstrap and default seed logic

### Gameplay
- `rooms/room_1.php` - first room
- `rooms/room_2.php` - second room with lockers and key trial
- `rooms/room_3.php` - final room and finish-time persistence
- `rooms/complete_room1.php` - endpoint for room 1 completion state

### Admin
- `admin/index.php`
- `admin/login.php`
- `admin/logout.php`
- `admin/auth.php`
- `admin/add_team.php`
- `admin/show_all_teams.php`
- `admin/add_riddle.php`
- `admin/show_all_riddles.php`
- `admin/add_review.php`
- `admin/show_all_reviews.php`

### Frontend
- `css/style.css`
- `js/menu.js`
- `js/join-riddle.js`
- `js/create-team-modal.js`
- `js/review-modal.js`
- `js/room1-scene.js`

## Technical Notes
- All database access uses PDO.
- Team and progress state are stored in PHP session.
- The schema is created automatically by `ensureProjectSchema(...)`.
- The project is organized by folder responsibility.

## Documentation
- `docs/simple_explanation.md`
- `docs/technical_explanation.md`
- `docs/extremely_detailed_technical_explanation.md`

PDF exports are generated in the same `docs/` folder.
