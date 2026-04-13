<!-- Direct New Job Request Modal -->
<div class="direct-job-modal-overlay" id="directJobRequestModal" aria-hidden="true">
    <div class="direct-job-modal-container" role="dialog" aria-modal="true" aria-labelledby="directJobRequestTitle">
        <div class="direct-job-modal-header">
            <h3 id="directJobRequestTitle">Request for a New Job</h3>
            <p class="direct-job-modal-subtitle">
                You are creating a direct request for
                <strong id="directJobRequestProviderLabel">this provider</strong>.
            </p>
            <button type="button" class="direct-job-close" id="directJobRequestCloseBtn" aria-label="Close">
                <i class="fas fa-times"></i>
            </button>
        </div>

        <div class="direct-job-modal-body">
            <div class="direct-job-error" id="directJobRequestError" style="display:none;"></div>
            <div class="direct-job-success" id="directJobRequestSuccess" style="display:none;"></div>

            <form id="directJobRequestForm" enctype="multipart/form-data">
                <input type="hidden" id="directJobProviderId" name="provider_id" value="">
                <input type="hidden" id="directJobProviderType" name="provider_type" value="">

                <div class="direct-job-form-group">
                    <label for="directJobTitle">Job Title <span class="required">*</span></label>
                    <input type="text" id="directJobTitle" name="title" required placeholder="e.g., Kitchen Sink Repair, AC Installation">
                </div>

                <div class="direct-job-form-group">
                    <label for="directJobCategory">Category <span class="required">*</span></label>
                    <select id="directJobCategory" name="category_id" required>
                        <option value="">Select a category</option>
                        <option value="1">Plumbing</option>
                        <option value="2">Electrical</option>
                        <option value="3">HVAC</option>
                        <option value="4">Cleaning</option>
                        <option value="5">Carpentry</option>
                        <option value="6">Painting</option>
                        <option value="7">Appliance Repair</option>
                    </select>
                </div>

                <div class="direct-job-form-group">
                    <label for="directJobDescription">Description <span class="required">*</span></label>
                    <textarea id="directJobDescription" name="description" rows="5" required placeholder="Describe your job in detail..."></textarea>
                </div>

                <div class="direct-job-two-col">
                    <div class="direct-job-form-group">
                        <label for="directJobDistrict">District <span class="required">*</span></label>
                        <select id="directJobDistrict" name="district" required>
                            <option value="">Select your district</option>
                            <option value="Colombo">Colombo</option>
                            <option value="Gampaha">Gampaha</option>
                            <option value="Kalutara">Kalutara</option>
                            <option value="Kandy">Kandy</option>
                            <option value="Matale">Matale</option>
                            <option value="Nuwara Eliya">Nuwara Eliya</option>
                            <option value="Galle">Galle</option>
                            <option value="Matara">Matara</option>
                            <option value="Hambantota">Hambantota</option>
                            <option value="Jaffna">Jaffna</option>
                            <option value="Kilinochchi">Kilinochchi</option>
                            <option value="Mannar">Mannar</option>
                            <option value="Vavuniya">Vavuniya</option>
                            <option value="Mullaitivu">Mullaitivu</option>
                            <option value="Batticaloa">Batticaloa</option>
                            <option value="Ampara">Ampara</option>
                            <option value="Trincomalee">Trincomalee</option>
                            <option value="Kurunegala">Kurunegala</option>
                            <option value="Puttalam">Puttalam</option>
                            <option value="Anuradhapura">Anuradhapura</option>
                            <option value="Polonnaruwa">Polonnaruwa</option>
                            <option value="Badulla">Badulla</option>
                            <option value="Monaragala">Monaragala</option>
                            <option value="Ratnapura">Ratnapura</option>
                            <option value="Kegalle">Kegalle</option>
                        </select>
                    </div>

                    <div class="direct-job-form-group">
                        <label for="directJobFinishDate">Expected Finish Date <span class="required">*</span></label>
                        <input type="date" id="directJobFinishDate" name="finish_date" required>
                    </div>
                </div>

                <div class="direct-job-form-group">
                    <label for="directJobAddress">Address <span class="required">*</span></label>
                    <input type="text" id="directJobAddress" name="address" required placeholder="Enter your full address (street, area)">
                </div>

                <div class="direct-job-form-group">
                    <label for="directJobPhotos">Photos (Optional)</label>
                    <input type="file" id="directJobPhotos" name="photos" accept="image/*">
                    <div id="directJobPhotoPreview" class="direct-job-photo-preview"></div>
                </div>
            </form>
        </div>

        <div class="direct-job-modal-actions">
            <button type="button" class="btn-outline" id="directJobRequestCancelBtn">Cancel</button>
            <button type="submit" form="directJobRequestForm" class="btn-primary" id="directJobRequestSubmitBtn">Request</button>
        </div>
    </div>
</div>
