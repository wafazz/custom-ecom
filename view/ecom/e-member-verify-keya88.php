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
                    <span>Email Verification
                    </span>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .verify-input {
        width: 42px;
        height: 50px;
        font-size: 20px;
        font-weight: 600;
    }

    @media (max-width: 576px) {
        .verify-input {
            width: 36px;
            height: 46px;
            font-size: 18px;
        }
    }
</style>
<!-- Breadcrumb End -->
<!-- Shop Cart Section Begin -->
<section class="shop-cart spad">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-6 col-md-8 col-sm-12">

                <div class="card shadow-sm border-0">
                    <div class="card-body p-4 text-center">

                        <h4 class="mb-2">Verify Your Email</h4>
                        <p class="text-muted mb-4">
                            Enter the <strong>6-digit verification code</strong> sent to your email.
                        </p>

                        <?php if (!empty($_SESSION['verify_error'])): ?>
                            <div class="alert alert-danger">
                                <?= $_SESSION['verify_error'];
                                unset($_SESSION['verify_error']); ?>
                            </div>
                        <?php endif; ?>

                        <form method="POST" action="">
                            <div class="d-flex justify-content-center mb-4 gap-2">

                                <?php for ($i = 0; $i < 6; $i++): ?>
                                    <input
                                        type="text"
                                        name="code[]"
                                        maxlength="1"
                                        class="form-control text-center verify-input"
                                        required>
                                <?php endfor; ?>

                            </div>

                            <button type="submit" name="verifyButton" class="btn btn-dark w-100">
                                Verify Account
                            </button>
                        </form>

                        <div class="mt-4">
                            <small class="text-muted">
                                Didn’t receive the code?
                                <a href="/resend-verification">Resend</a>
                            </small>
                        </div>

                    </div>
                </div>

            </div>
        </div>
    </div>
</section>

<script>
    document.querySelectorAll('.verify-input').forEach((input, index, inputs) => {
        input.addEventListener('input', () => {
            if (input.value && index < inputs.length - 1) {
                inputs[index + 1].focus();
            }
        });
        input.addEventListener('keydown', (e) => {
            if (e.key === 'Backspace' && !input.value && index > 0) {
                inputs[index - 1].focus();
            }
        });
    });
</script>

<!-- Shop Cart Section End -->

<?php
include "e-footer-keya88.php";
?>