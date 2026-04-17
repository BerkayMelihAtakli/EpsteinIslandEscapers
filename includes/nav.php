<nav>
    <div class="navContainer">
        <?php 
        $current_file = basename($_SERVER['PHP_SELF']);
        $is_room = in_array($current_file, ['room_1.php', 'room_2.php', 'room_3.php']);
        if (!empty($_SESSION['team_name']) && $is_room): 
        ?>
            <div class="team-badge">Team: <?php echo htmlspecialchars($_SESSION['team_name']); ?></div>
        <?php endif; ?>
        <div class="navLeft">
            <div class="navLeft">
                <div class="oog"><svg class="pointer" width="30" height="30" viewBox="0 0 100 100" fill="none"
                        xmlns="http://www.w3.org/2000/svg">
                        <path d="M5 50C5 50 25 15 50 15C75 15 95 50 95 50C95 50 75 85 50 85C25 85 5 50 5 50Z"
                            fill="#880404" class="pointer" />

                        <circle cx="50" cy="50" r="22" fill="#f5f2e9" class="pointer" />

                        <mask id="pupil-mask" class="pointer">
                            <circle cx="50" cy="50" r="14" fill="white" />
                            <circle cx="42" cy="42" r="7" fill="black" />
                        </mask>
                        <circle cx="50" cy="50" r="14" fill="#0d2818" mask="url(#pupil-mask)" class="pointer" />
                    </svg></div>

                <span class="title-text">Epstein Island</span>
            </div>

        </div>
        <div class="navRight">
            <ul class="big-ul">
                <li><a href="/EpsteinIslandEscapers/index.php" class="navNormalLink">Home</a></li>
                <li><a href="/EpsteinIslandEscapers/index.php#cult-riddle" class="navNormalLink">Join the Cult</a></li>
                <li><a href="/EpsteinIslandEscapers/create_team.php" class="navNormalLink">Create Team</a></li>
                <li>
                    <div class="joinCultDesign">
                        <a href="/EpsteinIslandEscapers/index.php#cult-riddle" class="joinCult">Join the Cult</a>
                    </div>
                </li>
            </ul>
        </div>
        <div class="burger-menu">
            <img src="/EpsteinIslandEscapers/assets/burger-menu.svg" alt="Burger Menu" class="burger-menu-icon pointer"
                id="burger-menu">
        </div>
        <div class="side-menu">
            <ul class="side-ul">
                <li class="b-li">
                    <div class="sideNavDesign">
                        <a href="/EpsteinIslandEscapers/index.php" class="sideNavLink">Home</a>
                    </div>
                </li>
                <li class="b-li">
                    <div class="sideNavDesign">
                        <a href="/EpsteinIslandEscapers/index.php#cult-riddle" class="sideNavLink">Join the Cult</a>
                    </div>
                </li>
                <li class="b-li">
                    <div class="sideNavDesign">
                        <a href="/EpsteinIslandEscapers/create_team.php" class="sideNavLink">Create Team</a>
                    </div>
                </li>
                <li class="sideInfoItem">
                    <h2 class="sideInfoTitle">Forgotten Whispers</h2>
                    <p class="sideInfoText">A quiet village hides old secrets and strange lights beyond the shore.</p>
                    <img src="/EpsteinIslandEscapers/assets/Misty island in a foggy sea.png"
                        alt="Misty island in a foggy sea" class="sideInfoImage">
                    <p class="sideInfoTextLong">Fog rolls over silent docks while distant bells echo through the night,
                        and every narrow path leads wanderers toward forgotten ruins, hidden symbols, and unanswered
                        names.</p>
                </li>
                <li class="b-li last-li">
                    <a href="/EpsteinIslandEscapers/index.php#cult-riddle" class="cult-btn cult-btn-a">Join the Cult</a>
                </li>
            </ul>
        </div>
    </div>
</nav>