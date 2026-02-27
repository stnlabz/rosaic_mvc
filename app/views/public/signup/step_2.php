<?php require APPROOT . '/views/inc/head.php'; ?>
<h2>Step 2: Birth Number Reduction</h2>
<p>Enter your birth date digits. No scrolling required.</p>

<form id="birth_form" method="POST" action="<?= URLROOT; ?>/signup/step_2">
    <div style="display: flex; gap: 10px; margin-bottom: 20px;">
        <div>
            <label style="display:block;">Month (MM)</label>
            <input type="number" name="mm" placeholder="05" min="1" max="12" required style="width: 60px;">
        </div>
        <div>
            <label style="display:block;">Day (DD)</label>
            <input type="number" name="dd" placeholder="15" min="1" max="31" required style="width: 60px;">
        </div>
        <div>
            <label style="display:block;">Year (YYYY)</label>
            <input type="number" name="yyyy" placeholder="1973" min="1900" max="<?= date('Y'); ?>" required style="width: 100px;">
        </div>
    </div>
    
    <button type="submit" name="calculate_birth_number">Calculate & Proceed</button>
</form>
<?php require APPROOT . '/views/inc/foot.php'; ?>
