<?php
$page = 'referral-link';
$url_prefix = '../';
include_once __DIR__ . '/includes/header.php';
include_once __DIR__ . '/../database/db_config.php';

// Auth Check
if (!isset($_SESSION['partner_id'])) {
    header("Location: login.php");
    exit;
}

$partner_id = $_SESSION['partner_id'];
$database = new Database();
$db = $database->getConnection();

// Fetch Partner's Referral Code
$stmt = $db->prepare("SELECT referral_code FROM partners WHERE id = :id");
$stmt->execute([':id' => $partner_id]);
$partner = $stmt->fetch(PDO::FETCH_ASSOC);
$referral_code = $partner['referral_code'];

// Generate Link (Dynamic Protocol/Host)
$protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http";
$host = $_SERVER['HTTP_HOST'];
// Assuming register.php is in root. Adjust path if strict subfolder.
// Since we are in /partner/, root is ../
// But for the URL to share, we need the full absolute URL.
// We can construct it based on known path.
$base_url = "$protocol://$host/warychary-india/"; // Adjust based on actual server path if needed, or dynamic
// For robustness, let's try to detect root.
// If localhost/warychary-india/partner/referral-link.php
// We want localhost/warychary-india/register.php
$path_parts = explode('/', $_SERVER['REQUEST_URI']);
// Remove last two parts (partner/referral-link.php)
array_pop($path_parts);
array_pop($path_parts);
$root_path = implode('/', $path_parts);
// Ensure trailing slash
if (substr($root_path, -1) !== '/') {
    $root_path .= '/';
}
// Fix double slash issue if root_path is just /
if ($root_path == '//') $root_path = '/';

// Final URL
$referral_link = "$protocol://$host" . $root_path . "register.php?ref=" . $referral_code;
?>

<div class="page-header">
    <div class="header-title">
        <h1 class="page-title">Referral Link</h1>
        <p class="text-muted">Share this link to invite users and earn commissions.</p>
    </div>
</div>

<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card text-center p-4">
            <div class="card-body">
                <div class="mb-4">
                    <img src="../assets/images/referral.svg" alt="Referral" class="img-fluid" style="max-height: 200px; opacity: 0.9;" onerror="this.style.display='none'">
                    <i class="fas fa-gift fa-4x text-primary mb-3" style="display: none;" onload="this.style.display='block'"></i>
                </div>
                
                <h3 class="card-title fw-bold mb-3">Invite Friends & Earn</h3>
                <p class="text-muted mb-4">Share your unique referral link with friends, family, and your network. When they register using this link, they will be automatically linked to you.</p>

                <div class="input-group mb-3">
                    <input type="text" class="form-control form-control-lg text-center bg-light" value="<?php echo $referral_link; ?>" id="referralLinkInput" readonly>
                    <button class="btn btn-primary px-4" type="button" onclick="copyLink()">
                        <i class="fas fa-copy me-2"></i> Copy
                    </button>
                </div>
                
                <div id="copyFeedback" class="text-success fw-bold small mb-4" style="opacity: 0; transition: opacity 0.3s;">
                    Link copied to clipboard!
                </div>

                <div class="social-share mt-4">
                    <p class="fw-medium text-uppercase text-muted small mb-3">Share via Social Media</p>
                    <div class="d-flex justify-content-center gap-3">
                        <a href="https://wa.me/?text=<?php echo urlencode("Join WaryChary using my referral link: " . $referral_link); ?>" target="_blank" class="btn btn-success rounded-circle p-3" style="width: 50px; height: 50px;">
                            <i class="fab fa-whatsapp fa-lg"></i>
                        </a>
                        <a href="https://www.facebook.com/sharer/sharer.php?u=<?php echo urlencode($referral_link); ?>" target="_blank" class="btn btn-primary rounded-circle p-3" style="width: 50px; height: 50px;">
                            <i class="fab fa-facebook-f fa-lg"></i>
                        </a>
                        <a href="https://twitter.com/intent/tweet?url=<?php echo urlencode($referral_link); ?>&text=<?php echo urlencode("Check out WaryChary!"); ?>" target="_blank" class="btn btn-info text-white rounded-circle p-3" style="width: 50px; height: 50px;">
                            <i class="fab fa-twitter fa-lg"></i>
                        </a>
                        <a href="mailto:?subject=Join WaryChary&body=<?php echo urlencode("Hi,\n\nI'd like to invite you to join WaryChary. Register using my link:\n" . $referral_link); ?>" class="btn btn-secondary rounded-circle p-3" style="width: 50px; height: 50px;">
                            <i class="fas fa-envelope fa-lg"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function copyLink() {
    var copyText = document.getElementById("referralLinkInput");
    copyText.select();
    copyText.setSelectionRange(0, 99999); // For mobile devices
    
    navigator.clipboard.writeText(copyText.value).then(function() {
        var feedback = document.getElementById("copyFeedback");
        feedback.style.opacity = "1";
        setTimeout(function() {
            feedback.style.opacity = "0";
        }, 2000);
    }, function(err) {
        console.error('Async: Could not copy text: ', err);
    });
}
</script>

<?php include_once __DIR__ . '/includes/footer.php'; ?>
