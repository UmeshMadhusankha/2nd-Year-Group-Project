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

drop-down gender in support ticket form

table->
ALTER TABLE support_tickets
ADD COLUMN gender enum('male','female','other','prefer_not_to_say') DEFAULT NULL;

view->
<!-- <div class="form-group">
    <label for="issue-gender" class="form-label">Gender</label>
    <select id="issue-gender" name="gender" class="form-input">
        <option value="" selected>Prefer not to say</option>
        <option value="male">Male</option>
        <option value="female">Female</option>
        <option value="other">Other</option>
        <option value="prefer_not_to_say">Prefer not to say</option>
    </select>
</div> 

<th>Gender</th> -->

js->
const gender = document.getElementById('issue-gender')?.value || '';
formData.append('gender', gender);

gender: ticket.gender || '',

<td class="ticket-gender">${formatTicketLabel(ticket.gender)}</td>

<td colspan="8" style="text-align:center;padding:40px;color:var(--text-secondary)">

api->
<php
$gender = trim((string)($_POST['gender'] ?? ($payload['gender'] ?? '')));

<php
'gender' => $gender === '' ? null : $gender

model->
<php
$sql = "INSERT INTO support_tickets 
        (ticket_number, user_type, user_id, title, category, priority, status, description, urgency, related_project_id, gender)
        VALUES
        (:ticket_number, 'repairer', :user_id, :title, :category, :priority, :status, :description, :urgency, :related_project_id, :gender)";

<php
':gender' => $data['gender']

<php
SELECT ticket_id, ticket_number, title, status, priority, urgency, gender, created_at, updated_at