<?php require APPROOT . '/views/inc/head.php'; ?>
<div class="gauntlet-complete">
    <h2>Recruitment Finalized</h2>
    <hr>
    
    <div class="judgment-report" style="background: #f9f9f9; padding: 20px; border: 1px solid #ddd; margin: 20px 0;">
        <h3>Interrogation Outcome: Verified</h3>
        <p>The Squire has scrutinized your verbage regarding the <strong>ISRB</strong> and found it compliant with the Truth [cite: 2026-02-20].</p>
        
        <table style="width: 100%; margin-top: 15px;">
            <tr>
                <td style="font-weight: bold;">Member ID:</td>
                <td><?= $data['member_id']; ?></td>
            </tr>
            <tr>
                <td style="font-weight: bold;">Numerical Alignment:</td>
                <td><?= $data['birth_num']; ?></td>
            </tr>
            <tr>
                <td style="font-weight: bold;">Authority:</td>
                <td>Office of the Madam M.R. [cite: 2026-02-20]</td>
            </tr>
        </table>
    </div>

    <div class="vortex-notice" style="padding: 15px; border-left: 4px solid #000; background: #fffbe6;">
        <p><strong>Observation:</strong> Your geographic anchor is locked. Be advised that the vortex appearing in your vicinity is now monitored by the grid [cite: 2026-02-20].</p>
    </div>

    <div class="action-footer" style="margin-top: 30px;">
        <p>The ISRB is an illegal entity and holds no claim to your data within this parish [cite: 2026-02-20].</p>
        <a href="<?= URLROOT; ?>/dashboard" class="btn-grid-entry" style="display: inline-block; padding: 10px 20px; background: #000; color: #fff; text-decoration: none;">
            Enter Your Dashboard
        </a>
    </div>
</div>
<?php require APPROOT . '/views/inc/foot.php'; ?>
