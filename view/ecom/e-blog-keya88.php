<?php
include "e-header-keya88.php";
include "e-menu-keya88.php";
?>

<!-- Breadcrumb Begin -->
<div class="breadcrumb-option">
    <div class="container">
        <div class="row">
            <div class="col-lg-12">
                <div class="breadcrumb__links">
                    <a href="<?= $domainURL ?>main"><i class="fa fa-home"></i> Home</a>

                    <span>
                        Announcement & Blog
                    </span>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- Breadcrumb End -->

<!-- Blog Section -->
<section class="product-details spad">
    <div class="container">
        <div class="row">

            <?php if (!empty($result)): ?>
                <?php foreach ($result as $row): ?>

                    <?php
                    $plainText = html_entity_decode(strip_tags($row['contents']), ENT_QUOTES | ENT_HTML5);
                    $plainText = trim(preg_replace('/\s+/', ' ', $plainText));
                    $words = explode(' ', $plainText);
                    $short = implode(' ', array_slice($words, 0, 50));
                    $fullContent = str_replace(
                        'src="assets/images/',
                        'src="https://rozeyana.com/assets/images/',
                        $row['contents']
                    );
                    ?>

                    <div class="col-lg-4 col-md-6 col-sm-12 mb-4">
                        <div style="border:1px solid #eee;padding:20px;height:100%;">

                            <h5><?= htmlspecialchars($row['title']) ?></h5>

                            <p>
                                <?= nl2br(htmlspecialchars($short)) ?>...
                            </p>

                            <button
                                type="button"
                                class="btn-show-more"
                                data-title="<?= htmlspecialchars($row['title'], ENT_QUOTES) ?>"
                                data-content="<?= htmlspecialchars($fullContent, ENT_QUOTES) ?>"
                                style="background:none;border:none;color:#007bff;padding:0;cursor:pointer;">
                                Show more
                            </button>

                            <br><br>

                            <small>
                                <?= date('d M Y', strtotime($row['created_at'])) ?> |
                                👁 <?= (int)$row['reader'] ?>
                            </small>

                        </div>
                    </div>

                <?php endforeach; ?>
            <?php else: ?>
                <div class="col-lg-12">
                    <p>No announcement available.</p>
                </div>
            <?php endif; ?>

        </div>

        <!-- Pagination -->
        <?php if ($totalPages > 1): ?>
            <div class="row mt-5">
                <div class="col-lg-12 text-center">
                    <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                        <?php if ($i == $page): ?>
                            <strong><?= $i ?></strong>
                        <?php else: ?>
                            <a href="?page=<?= $i ?>" style="margin:0 6px;"><?= $i ?></a>
                        <?php endif; ?>
                    <?php endfor; ?>
                </div>
            </div>
        <?php endif; ?>

    </div>
</section>

<!-- MODAL -->
<div id="blogModal" style="
    display:none;
    position:fixed;
    top:0;left:0;
    width:100%;height:100%;
    background:rgba(0,0,0,0.6);
    z-index:9999;
">
    <div style="
        background:#fff;
        max-width:900px;
        margin:40px auto;
        padding:25px;
        position:relative;
        max-height:85vh;
        overflow-y:auto;
    ">
        <button onclick="closeBlogModal()" style="
            position:absolute;
            right:15px;
            top:10px;
            background:none;
            border:none;
            font-size:22px;
            cursor:pointer;
        ">✕</button>

        <h3 id="modalTitle"></h3>
        <div id="modalContent"></div>
    </div>
</div>

<style>
    #modalContent img {
        max-width: 100%;
        height: auto;
    }
</style>

<script>
    document.querySelectorAll('.btn-show-more').forEach(function(btn) {
        btn.addEventListener('click', function() {
            var title = this.getAttribute('data-title');
            var content = this.getAttribute('data-content');

            document.getElementById('modalTitle').innerText = title;
            document.getElementById('modalContent').innerHTML = content;
            document.getElementById('blogModal').style.display = 'block';
            document.body.style.overflow = 'hidden';
        });
    });

    function closeBlogModal() {
        document.getElementById('blogModal').style.display = 'none';
        document.getElementById('modalContent').innerHTML = '';
        document.body.style.overflow = 'auto';
    }
</script>


<?php
include "e-footer-keya88.php";
?>