<?php
/**
 * تذييل الصفحة المشترك - Footer
 */
require_once __DIR__ . '/../config/asset_version.php';
?>
    </main>
    <footer class="main-footer">
        <p>© 2026 Digital Food Ordering System</p>
    </footer>
    <script src="<?php echo isset($basePath) ? $basePath : ''; ?>assets/js/main.js?v=<?php echo rawurlencode(ASSET_VERSION); ?>"></script>
</body>
</html>
