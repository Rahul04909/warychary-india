<?php
$page = 'bank-details';
require_once '../auth_check.php';
include_once '../database/db_config.php';
include_once 'includes/header.php';

$database = new Database();
$db = $database->getConnection();
$user_id = $_SESSION['partner_id'];
$user_type = 'partner';

$message = "";
$messageType = "";

// Handle Form Submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $account_holder_name = trim($_POST['account_holder_name']);
    $account_number = trim($_POST['account_number']);
    $confirm_account_number = trim($_POST['confirm_account_number']);
    $ifsc_code = strtoupper(trim($_POST['ifsc_code']));
    $bank_name = trim($_POST['bank_name']);
    $branch_address = trim($_POST['branch_address']);
    $bank_code = trim($_POST['bank_code']);

    if ($account_number !== $confirm_account_number) {
        $message = "Account numbers do not match!";
        $messageType = "danger";
    } else {
        try {
            // Check if record exists
            $checkStmt = $db->prepare("SELECT id FROM bank_details WHERE user_id = :uid AND user_type = :utype");
            $checkStmt->execute([':uid' => $user_id, ':utype' => $user_type]);
            $exists = $checkStmt->fetch();

            if ($exists) {
                // Update
                $sql = "UPDATE bank_details SET 
                        account_holder_name = :holder, 
                        account_number = :acc_num, 
                        ifsc_code = :ifsc, 
                        bank_name = :bank, 
                        branch_address = :branch, 
                        bank_code = :bcode 
                        WHERE id = :id";
                $stmt = $db->prepare($sql);
                $stmt->bindParam(':id', $exists['id']);
            } else {
                // Insert
                $sql = "INSERT INTO bank_details (user_id, user_type, account_holder_name, account_number, ifsc_code, bank_name, branch_address, bank_code) 
                        VALUES (:uid, :utype, :holder, :acc_num, :ifsc, :bank, :branch, :bcode)";
                $stmt = $db->prepare($sql);
                $stmt->bindParam(':uid', $user_id);
                $stmt->bindParam(':utype', $user_type);
            }

            $stmt->bindParam(':holder', $account_holder_name);
            $stmt->bindParam(':acc_num', $account_number);
            $stmt->bindParam(':ifsc', $ifsc_code);
            $stmt->bindParam(':bank', $bank_name);
            $stmt->bindParam(':branch', $branch_address);
            $stmt->bindParam(':bcode', $bank_code);

            if ($stmt->execute()) {
                $message = "Bank details saved successfully!";
                $messageType = "success";
            } else {
                $message = "Failed to save bank details.";
                $messageType = "danger";
            }

        } catch (PDOException $e) {
            $message = "Error: " . $e->getMessage();
            $messageType = "danger";
        }
    }
}

// Fetch Existing Details
$bankDetails = [];
try {
    $stmt = $db->prepare("SELECT * FROM bank_details WHERE user_id = :uid AND user_type = :utype");
    $stmt->execute([':uid' => $user_id, ':utype' => $user_type]);
    $bankDetails = $stmt->fetch(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    // Handle error
}
?>

<div class="page-header">
    <div class="header-title">
        <h1>Bank Details</h1>
        <p>Manage your payout information</p>
    </div>
</div>

<?php if (!empty($message)): ?>
    <div class="alert alert-<?php echo $messageType; ?> alert-dismissible fade show" role="alert">
        <?php echo $message; ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php endif; ?>

<div class="row">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-body">
                <form action="" method="POST" id="bankForm">
                    <h5 class="card-title mb-4">Bank Information</h5>
                    
                    <div class="mb-3">
                        <label class="form-label">Account Holder Name <span class="text-danger">*</span></label>
                        <input type="text" name="account_holder_name" class="form-control" required 
                               value="<?php echo htmlspecialchars($bankDetails['account_holder_name'] ?? ''); ?>" 
                               placeholder="Enter name as per bank records">
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">IFSC Code <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <input type="text" name="ifsc_code" id="ifsc_code" class="form-control text-uppercase" required 
                                       value="<?php echo htmlspecialchars($bankDetails['ifsc_code'] ?? ''); ?>" 
                                       placeholder="SBIN000XXXX" maxlength="11">
                                <button type="button" class="btn btn-outline-primary" id="fetchBankBtn">
                                    <i class="fas fa-search"></i> Verify
                                </button>
                            </div>
                            <small id="ifsc-feedback" class="text-muted"></small>
                        </div>
                         <div class="col-md-6 mb-3">
                            <label class="form-label">Bank Name</label>
                            <input type="text" name="bank_name" id="bank_name" class="form-control bg-light" readonly 
                                   value="<?php echo htmlspecialchars($bankDetails['bank_name'] ?? ''); ?>">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Branch Address</label>
                        <textarea name="branch_address" id="branch_address" class="form-control bg-light" readonly rows="2"><?php echo htmlspecialchars($bankDetails['branch_address'] ?? ''); ?></textarea>
                    </div>
                    
                    <input type="hidden" name="bank_code" id="bank_code" value="<?php echo htmlspecialchars($bankDetails['bank_code'] ?? ''); ?>">

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Account Number <span class="text-danger">*</span></label>
                            <input type="password" name="account_number" id="account_number" class="form-control" required 
                                   value="<?php echo htmlspecialchars($bankDetails['account_number'] ?? ''); ?>" 
                                   placeholder="Enter Account Number">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Confirm Account Number <span class="text-danger">*</span></label>
                            <input type="text" name="confirm_account_number" id="confirm_account_number" class="form-control" required 
                                   value="<?php echo htmlspecialchars($bankDetails['account_number'] ?? ''); ?>" 
                                   placeholder="Re-enter Account Number">
                             <div id="acc-match-feedback" class="form-text"></div>
                        </div>
                    </div>

                    <div class="mt-4">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-1"></i> Save Bank Details
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <div class="col-lg-4">
        <div class="card bg-light border-0">
            <div class="card-body">
                <h6 class="fw-bold"><i class="fas fa-info-circle text-primary me-2"></i>Important Note</h6>
                <ul class="small text-muted ps-3 mt-2 mb-0">
                    <li class="mb-1">Please ensure the bank account belongs to you.</li>
                    <li class="mb-1">Double check the Account Number and IFSC Code.</li>
                    <li class="mb-1">Incorrect details may verify lead to failed payouts.</li>
                    <li>Payments are processed securely via registered bank accounts only.</li>
                </ul>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const ifscInput = document.getElementById('ifsc_code');
    const fetchBtn = document.getElementById('fetchBankBtn');
    const feedback = document.getElementById('ifsc-feedback');
    
    // Auto-fetch logic
    fetchBtn.addEventListener('click', fetchBankDetails);
    
    ifscInput.addEventListener('blur', function() {
        if(this.value.length === 11) fetchBankDetails();
    });

    function fetchBankDetails() {
        const ifsc = ifscInput.value.trim().toUpperCase();
        if (ifsc.length !== 11) {
            feedback.innerHTML = '<span class="text-danger">Invalid IFSC Code length</span>';
            return;
        }

        feedback.innerHTML = '<span class="text-info"><i class="fas fa-spinner fa-spin"></i> Fetching details...</span>';
        
        // Using Razorpay IFSC API
        fetch(`https://ifsc.razorpay.com/${ifsc}`)
            .then(response => {
                if (!response.ok) throw new Error('Bank not found');
                return response.json();
            })
            .then(data => {
                document.getElementById('bank_name').value = data.BANK;
                document.getElementById('branch_address').value = data.ADDRESS;
                document.getElementById('bank_code').value = data.BANKCODE;
                feedback.innerHTML = '<span class="text-success"><i class="fas fa-check-circle"></i> Verified: ' + data.BRANCH + '</span>';
            })
            .catch(error => {
                console.error(error);
                feedback.innerHTML = '<span class="text-danger"><i class="fas fa-times-circle"></i> Invalid IFSC Code</span>';
                document.getElementById('bank_name').value = '';
                document.getElementById('branch_address').value = '';
                document.getElementById('bank_code').value = '';
            });
    }

    // Account Match Validation
    const accInput = document.getElementById('account_number');
    const confirmInput = document.getElementById('confirm_account_number');
    const matchFeedback = document.getElementById('acc-match-feedback');

    function checkMatch() {
        if (confirmInput.value && accInput.value) {
            if (accInput.value === confirmInput.value) {
                confirmInput.classList.remove('is-invalid');
                confirmInput.classList.add('is-valid');
                matchFeedback.innerHTML = '<span class="text-success">Account numbers match</span>';
            } else {
                confirmInput.classList.remove('is-valid');
                confirmInput.classList.add('is-invalid');
                 matchFeedback.innerHTML = '<span class="text-danger">Account numbers do not match</span>';
            }
        } else {
             confirmInput.classList.remove('is-valid', 'is-invalid');
             matchFeedback.innerHTML = '';
        }
    }

    accInput.addEventListener('input', checkMatch);
    confirmInput.addEventListener('input', checkMatch);
});
</script>

<?php include_once 'includes/footer.php'; ?>
