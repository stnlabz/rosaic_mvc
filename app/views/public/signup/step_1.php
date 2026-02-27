<?php require APPROOT . '/views/inc/head.php'; ?>
<h2>Step 1: Geographic Anchor</h2>

<form id="signup_form" method="POST" action="<?= URLROOT; ?>/signup/step_1">
    
    <label for="region_id">Region</label>
    <select id="region_id" name="region_id" required>
        <option value="">Select Region...</option>
        <?php foreach($data['regions'] as $region): ?>
            <option value="<?= $region['id']; ?>"><?= ucfirst($region['name']); ?></option>
        <?php endforeach; ?>
    </select>

    <label for="state_id">State</label>
    <select id="state_id" name="state_id" required disabled>
        <option value="">Select Region First...</option>
    </select>

    <label for="county_id">County/Parish</label>
    <select id="county_id" name="county_id" required disabled>
        <option value="">Select State First...</option>
    </select>

    <button type="submit">Proceed to Step 2</button>
</form>

<script>
const root = '<?= URLROOT; ?>';
const regionSelect = document.querySelector('#region_id');
const stateSelect = document.querySelector('#state_id');
const countySelect = document.querySelector('#county_id');

regionSelect.addEventListener('change', function() {
    stateSelect.innerHTML = '<option value="">loading...</option>';
    stateSelect.disabled = true;
    if (this.value) {
        fetch(`${root}/signup/get_states/${this.value}`)
            .then(res => res.json())
            .then(data => {
                stateSelect.innerHTML = '<option value="">select state...</option>';
                data.forEach(state => {
                    let opt = document.createElement('option');
                    opt.value = state.id; opt.text = state.name;
                    stateSelect.add(opt);
                });
                stateSelect.disabled = false;
            });
    }
});

stateSelect.addEventListener('change', function() {
    countySelect.innerHTML = '<option value="">loading...</option>';
    countySelect.disabled = true;
    if (this.value) {
        fetch(`${root}/signup/get_counties/${this.value}`)
            .then(res => res.json())
            .then(data => {
                countySelect.innerHTML = '<option value="">select county...</option>';
                data.forEach(county => {
                    let opt = document.createElement('option');
                    opt.value = county.id; opt.text = county.name;
                    countySelect.add(opt);
                });
                countySelect.disabled = false;
            });
    }
});
</script>
<?php require APPROOT . '/views/inc/foot.php'; ?>
