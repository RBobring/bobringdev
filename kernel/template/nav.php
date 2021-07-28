<aside data-role="nav">
    <nav>
        <ul>
            <li data-icon="home">Startseite</li>
            <?php foreach($system::$content['nav']['main'] as $link): ?>
                <li data-icon="sign"><?php echo $link ?></li>
            <?php endforeach; ?>
        </ul>
    </nav>
</aside>