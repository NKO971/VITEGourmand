</main> 
    </div> 
</div> 

<!-- Scripts JS -->
<?php if (!empty($specificJs)): ?>
    <?php foreach ($specificJs as $js): ?>
        <script src="<?= $js ?>"></script>
    <?php endforeach; ?>
<?php endif; ?>

</body>
</html>