<?php
// Start session only if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Include components
require_once __DIR__ . '/_components/Sidebar.php';
require_once __DIR__ . '/_components/Meta.php';
require_once __DIR__ . '/_components/Header.php';
require_once __DIR__ . '/_components/Common.php';
require_once __DIR__ . '/../../includes/admin-modarator/auth.php';
require_once __DIR__ . '/../../includes/admin-modarator/mock-data.php';

// Check if user is logged in and get user info
// $isLoggedIn = isLoggedIn();
// $user = $isLoggedIn ? getCurrentUser() : null;
// requireRole("moderator", $basePath);
// $user = getCurrentUser();

$message = '';
$basePath = '';
$currentPath = 'static-content';

// Handle form submission
$message = '';
if ($_POST) {
    if (isset($_POST['action']) && $_POST['action'] === 'update_content') {
        $message = 'Content updated successfully!';
    }
}

$contentSections = [
    [
        'id' => 'homepage_hero',
        'title' => 'Homepage Hero Section',
        'description' => 'Main banner text and call-to-action',
        'content' => 'Find trusted service providers in Sri Lanka. Get your home repairs done by verified professionals.',
        'lastUpdated' => '2024-07-20',
        'status' => 'Published'
    ],
    [
        'id' => 'about_us',
        'title' => 'About Us Page',
        'description' => 'Company information and mission statement',
        'content' => 'FixLanka connects homeowners with reliable service providers across Sri Lanka. Our platform ensures quality service delivery through verified professionals.',
        'lastUpdated' => '2024-07-18',
        'status' => 'Published'
    ],
    [
        'id' => 'faq',
        'title' => 'FAQ Section',
        'description' => 'Frequently asked questions and answers',
        'content' => 'Common questions about our services, pricing, and how to get started.',
        'lastUpdated' => '2024-07-15',
        'status' => 'Draft'
    ],
    [
        'id' => 'terms',
        'title' => 'Terms of Service',
        'description' => 'Legal terms and conditions',
        'content' => 'Terms and conditions governing the use of FixLanka platform.',
        'lastUpdated' => '2024-07-10',
        'status' => 'Published'
    ],
    [
        'id' => 'privacy',
        'title' => 'Privacy Policy',
        'description' => 'Data protection and privacy information',
        'content' => 'How we collect, use, and protect your personal information.',
        'lastUpdated' => '2024-07-08',
        'status' => 'Published'
    ]
];

// Get page title and description from variables or use defaults
$pageTitle = $title ?? 'Advanced PHP Router';
$pageDescription = $description ?? 'A Next.js-inspired PHP routing system with advanced features';
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <?php renderMeta($pageTitle, $pageDescription, $basePath ?? ''); ?>
    <script src="https://unpkg.com/lucide@latest/dist/umd/lucide.js"></script>

    <!-- Page Styles -->
    <link rel="stylesheet" href="/2nd-Year-Group-Project/FixLanka/assets/css/moderator/dashboard.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="/2nd-Year-Group-Project/FixLanka/assets/css/moderator/static-content.css?v=<?php echo time(); ?>">

</head>

<body class="bg-foreground text-background">
    <!-- Sidebar Toggle Checkbox -->
    <input type="checkbox" id="sidebar-toggle" class="sidebar-toggle-input">

    <div class="dashboard-container">
        <?php renderModeratorSidebar($currentPath, $basePath); ?>
        <div class="dashboard-main">
            <?php renderPageHeader($basePath, 'Static Content Management', 'Edit and manage website content'); ?>

            <main class="dashboard-content">
                <div class="space-y-6">
                    <div>
                        <h2 class="text-3xl font-bold tracking-tight text-foreground">Static Content Management</h2>
                        <p class="text-muted-foreground">Edit and manage website content, pages, and information</p>
                    </div>

                    <?php if ($message): ?>
                        <div class="alert alert-success">
                            <?php echo htmlspecialchars($message); ?>
                        </div>
                    <?php endif; ?>

                    <div class="grid gap-4 md-grid-cols-4">
                        <?php
                        $publishedCount = count(array_filter($contentSections, fn($section) => $section['status'] === 'Published'));
                        $draftCount = count(array_filter($contentSections, fn($section) => $section['status'] === 'Draft'));

                        renderCard('Total Sections', count($contentSections), 'Content sections', 'file-text', 'blue');
                        renderCard('Published', $publishedCount, 'Live content', 'check-circle', 'green');
                        renderCard('Drafts', $draftCount, 'Unpublished content', 'edit', 'yellow');
                        renderCard('Last Updated', '2 days ago', 'Most recent change', 'clock', 'purple');
                        ?>
                    </div>

                    <div class="rounded-lg border bg-card">
                        <div class="p-6 border-b">
                            <h3 class="text-lg font-medium text-foreground">Content Sections</h3>
                            <p class="text-sm text-muted-foreground">Manage all static content sections of the website</p>
                        </div>

                        <div class="divide-y">
                            <?php foreach ($contentSections as $section): ?>
                                <div class="p-6 flex items-start justify-between gap-3" data-section-id="<?php echo $section['id']; ?>">
                                    <div class="flex-1">
                                        <div class="flex items-start justify-between gap-3 mb-3">
                                            <div>
                                                <h4 class="text-lg font-semibold text-foreground"><?php echo $section['title']; ?></h4>
                                                <p class="text-sm text-muted-foreground"><?php echo $section['description']; ?></p>
                                            </div>
                                            <?php
                                            $isPublished = ($section['status'] === 'Published');
                                            $badgeClass = $isPublished ? 'badge badge-default' : 'badge badge-secondary';
                                            ?>
                                            <span class="<?php echo $badgeClass; ?>" data-status><?php echo htmlspecialchars($section['status']); ?></span>
                                        </div>

                                        <div class="content-preview">
                                            <p data-content><?php echo htmlspecialchars($section['content']); ?></p>
                                        </div>
                                        <p class="text-xs text-muted-foreground">Last updated: <?php echo $section['lastUpdated']; ?></p>
                                    </div>

                                    <div class="content-actions flex gap-2">
                                        <button
                                            onclick="editContent('<?php echo $section['id']; ?>', '<?php echo addslashes($section['title']); ?>', '<?php echo addslashes($section['content']); ?>', '<?php echo $section['status']; ?>')"
                                            class="btn btn-secondary">
                                            <i data-lucide="edit" class="h-4 w-4"></i>
                                            Edit
                                        </button>
                                        <?php if ($section['status'] === 'Draft'): ?>
                                            <button
                                                onclick="confirmPublish('<?php echo $section['id']; ?>', '<?php echo addslashes($section['title']); ?>')"
                                                class="btn btn-primary">
                                                <i data-lucide="upload" class="h-4 w-4"></i>
                                                Publish
                                            </button>
                                        <?php else: ?>
                                            <button
                                                onclick="confirmUnpublish('<?php echo $section['id']; ?>', '<?php echo addslashes($section['title']); ?>')"
                                                class="btn btn-secondary">
                                                <i data-lucide="archive" class="h-4 w-4"></i>
                                                Unpublish
                                            </button>
                                        <?php endif; ?>
                                        <button
                                            onclick="confirmDelete('<?php echo $section['id']; ?>', '<?php echo addslashes($section['title']); ?>')"
                                            class="btn btn-danger">
                                            <i data-lucide="trash-2" class="h-4 w-4"></i>
                                            Delete
                                        </button>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <div class="section-card">
                        <h3 class="section-title">Quick Actions</h3>
                        <div class="quick-actions-grid">
                            <button class="action-card action-teal" type="button" onclick="addNewSection()">
                                <div class="stat-icon"><i data-lucide="plus"></i></div>
                                <div>
                                    <div class="text-lg font-semibold text-foreground">Add New Section</div>
                                    <div class="text-sm text-muted-foreground">Create a new content section</div>
                                </div>
                            </button>
                            <button class="action-card action-green" type="button" onclick="bulkPublish()">
                                <div class="stat-icon"><i data-lucide="upload"></i></div>
                                <div>
                                    <div class="text-lg font-semibold text-foreground">Bulk Publish</div>
                                    <div class="text-sm text-muted-foreground">Publish all draft content</div>
                                </div>
                            </button>
                            <button class="action-card action-blue" type="button" onclick="exportContent()">
                                <div class="stat-icon"><i data-lucide="download"></i></div>
                                <div>
                                    <div class="text-lg font-semibold text-foreground">Export Content</div>
                                    <div class="text-sm text-muted-foreground">Download content for backup</div>
                                </div>
                            </button>
                        </div>
                    </div>
                </div>
            </main>

            <!-- Edit Content Modal -->
            <div id="editModal" class="modal-overlay">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h3 class="modal-title">
                                <i data-lucide="edit" class="h-5 w-5 mr-2"></i>
                                Edit Content
                            </h3>
                            <button type="button" onclick="closeModal('editModal')" class="modal-close">
                                <i data-lucide="x" class="h-5 w-5"></i>
                            </button>
                        </div>
                        <div class="modal-body">
                            <form id="editForm" class="space-y-4">
                                <input type="hidden" id="editSectionId">

                                <div class="form-group">
                                    <label class="form-label">Section Title</label>
                                    <input type="text" id="editTitle" readonly class="form-input bg-muted">
                                </div>

                                <div class="form-group">
                                    <label class="form-label">Content</label>
                                    <textarea id="editContent" rows="8" class="form-textarea"></textarea>
                                </div>

                                <div class="form-group">
                                    <label class="form-label">Status</label>
                                    <select id="editStatus" class="form-select">
                                        <option value="Draft">Draft</option>
                                        <option value="Published">Published</option>
                                    </select>
                                </div>
                            </form>
                        </div>
                        <div class="modal-footer">
                            <button type="button" onclick="closeModal('editModal')" class="btn btn-secondary">
                                Cancel
                            </button>
                            <button type="button" onclick="saveContent()" class="btn btn-primary">
                                <i data-lucide="save" class="h-4 w-4 mr-1"></i>
                                Save Changes
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Confirmation Modal -->
            <div id="confirmModal" class="modal-overlay">
                <div class="modal-dialog modal-sm">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h3 class="modal-title" id="confirmTitle">Confirm Action</h3>
                            <button type="button" onclick="closeModal('confirmModal')" class="modal-close">
                                <i data-lucide="x" class="h-5 w-5"></i>
                            </button>
                        </div>
                        <div class="modal-body">
                            <div id="confirmIcon" class="confirmation-icon">
                                <i data-lucide="alert-circle" class="h-6 w-6"></i>
                            </div>
                            <p id="confirmMessage" class="confirmation-text"></p>
                            <div id="confirmDetails" class="confirmation-details" style="display: none;"></div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" onclick="closeModal('confirmModal')" class="btn btn-secondary">
                                Cancel
                            </button>
                            <button type="button" id="confirmButton" class="btn btn-primary">
                                Confirm
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <script>
                document.addEventListener('DOMContentLoaded', function() {
                    lucide.createIcons();
                });

                function editContent(sectionId, title, content, status) {
                    document.getElementById('editSectionId').value = sectionId;
                    document.getElementById('editTitle').value = title;
                    document.getElementById('editContent').value = content;
                    document.getElementById('editStatus').value = status;

                    openModal('editModal');
                }

                function saveContent() {
                    const sectionId = document.getElementById('editSectionId').value;
                    const newContent = document.getElementById('editContent').value;
                    const newStatus = document.getElementById('editStatus').value;

                    // Update the UI with demo data
                    const section = document.querySelector(`[data-section-id="${sectionId}"]`);
                    if (section) {
                        const contentElement = section.querySelector('[data-content]');
                        const statusBadge = section.querySelector('[data-status]');
                        const actionsContainer = section.querySelector('.content-actions');

                        if (contentElement) {
                            contentElement.textContent = newContent;
                        }

                        if (statusBadge) {
                            statusBadge.textContent = newStatus;
                            statusBadge.className = newStatus === 'Published' ? 'badge badge-default' : 'badge badge-secondary';
                        }

                        // Update action buttons based on new status
                        updateActionButtons(actionsContainer, sectionId, newStatus);
                    }

                    closeModal('editModal');
                    showSuccessMessage('Content updated successfully!');
                }

                function updateActionButtons(container, sectionId, status) {
                    const section = document.querySelector(`[data-section-id="${sectionId}"]`);
                    const title = section.querySelector('h4').textContent;

                    // Find and update the publish/unpublish button
                    const buttons = container.querySelectorAll('button');
                    buttons.forEach(btn => {
                        if (btn.textContent.includes('Publish') || btn.textContent.includes('Unpublish')) {
                            btn.remove();
                        }
                    });

                    // Add appropriate button
                    const editButton = container.querySelector('button');
                    if (status === 'Draft') {
                        const publishBtn = document.createElement('button');
                        publishBtn.className = 'btn btn-primary';
                        publishBtn.onclick = () => confirmPublish(sectionId, title);
                        publishBtn.innerHTML = '<i data-lucide="upload" class="h-4 w-4"></i> Publish';
                        editButton.after(publishBtn);
                    } else {
                        const unpublishBtn = document.createElement('button');
                        unpublishBtn.className = 'btn btn-secondary';
                        unpublishBtn.onclick = () => confirmUnpublish(sectionId, title);
                        unpublishBtn.innerHTML = '<i data-lucide="archive" class="h-4 w-4"></i> Unpublish';
                        editButton.after(unpublishBtn);
                    }

                    lucide.createIcons();
                }

                function confirmPublish(sectionId, title) {
                    const confirmIcon = document.getElementById('confirmIcon');
                    confirmIcon.className = 'confirmation-icon success';
                    confirmIcon.innerHTML = '<i data-lucide="upload" class="h-6 w-6" style="color: var(--success-color);"></i>';

                    document.getElementById('confirmTitle').textContent = 'Publish Content';
                    document.getElementById('confirmMessage').textContent = 'Are you sure you want to publish this content section? It will be visible to all users.';

                    const details = document.getElementById('confirmDetails');
                    details.style.display = 'block';
                    details.innerHTML = `<strong>Section:</strong> ${title}<br><strong>Action:</strong> Draft → Published`;

                    const confirmBtn = document.getElementById('confirmButton');
                    confirmBtn.className = 'btn btn-primary';
                    confirmBtn.innerHTML = '<i data-lucide="check" class="h-4 w-4 mr-1"></i> Publish';
                    confirmBtn.onclick = () => publishContent(sectionId);

                    openModal('confirmModal');
                    lucide.createIcons();
                }

                function confirmUnpublish(sectionId, title) {
                    const confirmIcon = document.getElementById('confirmIcon');
                    confirmIcon.className = 'confirmation-icon';
                    confirmIcon.innerHTML = '<i data-lucide="archive" class="h-6 w-6" style="color: var(--warning-color);"></i>';

                    document.getElementById('confirmTitle').textContent = 'Unpublish Content';
                    document.getElementById('confirmMessage').textContent = 'Are you sure you want to unpublish this content section? It will no longer be visible to users.';

                    const details = document.getElementById('confirmDetails');
                    details.style.display = 'block';
                    details.innerHTML = `<strong>Section:</strong> ${title}<br><strong>Action:</strong> Published → Draft`;

                    const confirmBtn = document.getElementById('confirmButton');
                    confirmBtn.className = 'btn btn-secondary';
                    confirmBtn.innerHTML = '<i data-lucide="archive" class="h-4 w-4 mr-1"></i> Unpublish';
                    confirmBtn.onclick = () => unpublishContent(sectionId);

                    openModal('confirmModal');
                    lucide.createIcons();
                }

                function confirmDelete(sectionId, title) {
                    const confirmIcon = document.getElementById('confirmIcon');
                    confirmIcon.className = 'confirmation-icon danger';
                    confirmIcon.innerHTML = '<i data-lucide="trash-2" class="h-6 w-6" style="color: var(--danger-color);"></i>';

                    document.getElementById('confirmTitle').textContent = 'Delete Content';
                    document.getElementById('confirmMessage').textContent = 'Are you sure you want to delete this content section? This action cannot be undone.';

                    const details = document.getElementById('confirmDetails');
                    details.style.display = 'block';
                    details.innerHTML = `<strong>Section:</strong> ${title}<br><strong>Warning:</strong> This is permanent!`;

                    const confirmBtn = document.getElementById('confirmButton');
                    confirmBtn.className = 'btn btn-danger';
                    confirmBtn.innerHTML = '<i data-lucide="trash-2" class="h-4 w-4 mr-1"></i> Delete';
                    confirmBtn.onclick = () => deleteContent(sectionId);

                    openModal('confirmModal');
                    lucide.createIcons();
                }

                function publishContent(sectionId) {
                    const section = document.querySelector(`[data-section-id="${sectionId}"]`);
                    if (section) {
                        const statusBadge = section.querySelector('[data-status]');
                        const actionsContainer = section.querySelector('.content-actions');

                        if (statusBadge) {
                            statusBadge.textContent = 'Published';
                            statusBadge.className = 'status-badge published';
                        }

                        updateActionButtons(actionsContainer, sectionId, 'Published');
                    }

                    closeModal('confirmModal');
                    showSuccessMessage('Content published successfully!');
                }

                function unpublishContent(sectionId) {
                    const section = document.querySelector(`[data-section-id="${sectionId}"]`);
                    if (section) {
                        const statusBadge = section.querySelector('[data-status]');
                        const actionsContainer = section.querySelector('.content-actions');

                        if (statusBadge) {
                            statusBadge.textContent = 'Draft';
                            statusBadge.className = 'status-badge draft';
                        }

                        updateActionButtons(actionsContainer, sectionId, 'Draft');
                    }

                    closeModal('confirmModal');
                    showSuccessMessage('Content unpublished successfully!');
                }

                function deleteContent(sectionId) {
                    const section = document.querySelector(`[data-section-id="${sectionId}"]`);
                    if (section) {
                        section.style.transition = 'all 0.3s ease';
                        section.style.opacity = '0';
                        section.style.transform = 'translateX(-20px)';

                        setTimeout(() => {
                            section.remove();
                        }, 300);
                    }

                    closeModal('confirmModal');
                    showSuccessMessage('Content deleted successfully!');
                }

                function addNewSection() {
                    alert('Add New Section functionality - This would open a form to create a new content section');
                }

                function bulkPublish() {
                    alert('Bulk Publish functionality - This would publish all draft sections at once');
                }

                function exportContent() {
                    alert('Export Content functionality - This would download all content as JSON or CSV');
                }

                function openModal(modalId) {
                    const modal = document.getElementById(modalId);
                    if (modal) {
                        modal.classList.add('show');
                        document.body.style.overflow = 'hidden';
                    }
                }

                function closeModal(modalId) {
                    const modal = document.getElementById(modalId);
                    if (modal) {
                        modal.classList.remove('show');
                        document.body.style.overflow = '';
                    }
                }

                document.querySelectorAll('.modal-overlay').forEach(overlay => {
                    overlay.addEventListener('click', function(e) {
                        if (e.target === this) {
                            closeModal(this.id);
                        }
                    });
                });

                function showSuccessMessage(message) {
                    const existingMessage = document.querySelector('.alert');
                    if (existingMessage) {
                        existingMessage.remove();
                    }

                    const messageDiv = document.createElement('div');
                    messageDiv.className = 'alert alert-success';
                    messageDiv.textContent = message;

                    const container = document.querySelector('.space-y-6');
                    container.insertBefore(messageDiv, container.children[1]);

                    setTimeout(() => {
                        messageDiv.style.transition = 'all 0.3s ease';
                        messageDiv.style.opacity = '0';
                        messageDiv.style.transform = 'translateY(-10px)';
                        setTimeout(() => messageDiv.remove(), 300);
                    }, 3000);
                }
            </script>
        </div>
    </div>
    <script>
        lucide.createIcons();
    </script>
    <script src="/2nd-Year-Group-Project/FixLanka/assets/javascript/admin-moderator/common.js"></script>

</body>

</html>

