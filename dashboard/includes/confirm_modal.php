<div class="modal-overlay" id="confirmModal">
    <div class="modal-box">
        <div class="modal-icon">&#33;</div>
        <h3 id="confirmModalTitle">Are you sure?</h3>
        <p id="confirmModalMessage">This action cannot be undone.</p>
        <div class="modal-actions">
            <button type="button" class="modal-cancel-btn" id="confirmModalCancel">Cancel</button>
            <button type="button" class="btn btn-danger" id="confirmModalConfirm">Yes, Delete</button>
        </div>
    </div>
</div>

<script>
    (function() {
        var overlay = document.getElementById('confirmModal');
        var messageEl = document.getElementById('confirmModalMessage');
        var confirmBtn = document.getElementById('confirmModalConfirm');
        var cancelBtn = document.getElementById('confirmModalCancel');
        var pendingForm = null;

        document.querySelectorAll('.js-confirm-delete').forEach(function(form) {
            form.addEventListener('submit', function(e) {
                e.preventDefault();
                pendingForm = form;
                messageEl.textContent = form.getAttribute('data-message') || 'This action cannot be undone.';
                overlay.classList.add('active');
            });
        });

        function closeModal() {
            overlay.classList.remove('active');
            pendingForm = null;
        }

        cancelBtn.addEventListener('click', closeModal);

        overlay.addEventListener('click', function(e) {
            if (e.target === overlay) closeModal();
        });

        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape' && overlay.classList.contains('active')) closeModal();
        });

        confirmBtn.addEventListener('click', function() {
            if (pendingForm) pendingForm.submit();
        });
    })();
</script>