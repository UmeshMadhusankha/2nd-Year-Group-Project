function isValidPhone(v) {
    return /^(0\d{9}|\+94\d{9})$/.test(v);
}

ALTER TABLE repairer DROP COLUMN id_number;

                                            <!-- <div class="form-group">
                                                <label for="id-number" class="form-label">ID Number</label>
                                                <input type="text" id="id-number" name="id-number" class="form-input" value="" readonly>
                                            </div> -->

for new field id_number;
    ALTER TABLE repairer;
    ADD COLUMN id_number varchar(50) DEFAULT NULL;


for JS 
    setVal('id-number', data.id_number || ''); - populateProfileData(data)
    id_number: getVal('id-number'), - handleSaveChanges()

for repairerModel.php
    r.id_number, - in getAll/getById
    'id_number = ?', - in $fields/$values

for RepairerController.php
    'id_number' => $input['id_number'] ?? null, - in $data

