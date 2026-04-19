<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User CRUD Reference</title>
    <link rel="stylesheet" href="/2nd-Year-Group-Project/FixLanka/dev-reference/user-crud/assets/css/user/crud_reference.css">
</head>
<body>
    <main class="ref-wrap">
        <header class="ref-header">
            <h1>User CRUD + Join Logic Reference</h1>
            <p>Practice page for viva tasks: add field, CRUD, and business SQL joins.</p>
        </header>

        <section class="ref-card">
            <h2>Task 1 Pattern: Add New Form Field</h2>
            <p>
                Example new field is <strong>priority_level</strong>.
                In real project, you add this to form + controller validation + model queries + DB column.
            </p>
            <div class="tip-box">
                SQL: ALTER TABLE jobrequest ADD COLUMN priority_level ENUM('low','medium','high') NOT NULL DEFAULT 'medium';
            </div>
        </section>

        <section class="ref-card">
            <h2>Task 2 Pattern: User CRUD (Practice Table)</h2>

            <!--
                This form demonstrates:
                - normal text inputs
                - a "newly added" field (priority_level)
                - hidden note_id for update mode
            -->
            <form id="noteForm" class="ref-form">
                <input type="hidden" id="noteId" value="">

                <label for="noteTitle">Title</label>
                <input id="noteTitle" type="text" maxlength="120" required placeholder="Enter note title">

                <label for="noteBody">Body</label>
                <textarea id="noteBody" rows="3" placeholder="Optional details"></textarea>

                <label for="priorityLevel">Priority Level (new field)</label>
                <select id="priorityLevel" required>
                    <option value="low">Low</option>
                    <option value="medium" selected>Medium</option>
                    <option value="high">High</option>
                </select>

                <div class="button-row">
                    <button type="submit" id="saveBtn">Save</button>
                    <button type="button" id="resetBtn">Reset</button>
                </div>
            </form>

            <p id="noteMessage" class="message"></p>

            <table class="ref-table" id="notesTable">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Title</th>
                        <th>Priority</th>
                        <th>Created</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody id="notesTbody">
                    <tr><td colspan="5">Loading notes...</td></tr>
                </tbody>
            </table>
        </section>

        <section class="ref-card">
            <h2>Business Logic Join Output</h2>
            <p>Joined data from jobrequest + quotes + review (read-only insight).</p>
            <button type="button" id="refreshInsightsBtn">Refresh Insights</button>
            <table class="ref-table" id="insightsTable">
                <thead>
                    <tr>
                        <th>Request ID</th>
                        <th>Job Title</th>
                        <th>Status</th>
                        <th>Priority</th>
                        <th>Agreed Amount</th>
                        <th>Provider Type</th>
                        <th>Rating</th>
                    </tr>
                </thead>
                <tbody id="insightsTbody">
                    <tr><td colspan="7">Loading insights...</td></tr>
                </tbody>
            </table>
        </section>
    </main>

    <script src="/2nd-Year-Group-Project/FixLanka/dev-reference/user-crud/assets/javascript/user/crud_reference.js"></script>
</body>
</html>
