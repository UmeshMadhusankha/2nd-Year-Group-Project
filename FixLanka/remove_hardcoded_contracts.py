import re

# Read the file
with open(r'c:\xampp\htdocs\2nd-Year-Group-Project\FixLanka\views\company\contracts.php', 'r', encoding='utf-8') as f:
    content = f.read()

# Find and replace the hardcoded cards section
# We'll match from "<!-- Contract Card 1 -->" to the line before "<!-- Load More / Infinite Scroll -->"
pattern = r'(<!-- Contracts List/Grid -->\s*<div class="contracts-container" id="contractsContainer">)\s*<!-- Contract Card 1 -->.*?</div>\s*</div>\s*(?=<!-- Load More / Infinite Scroll -->)'

replacement = r'''\1
                    <!-- Loading State -->
                    <div class="contracts-loading" id="contractsLoading">
                        <div class="loading-spinner"></div>
                        <p>Loading contracts...</p>
                    </div>

                    <!-- Empty State -->
                    <div class="contracts-empty" id="contractsEmpty" style="display: none;">
                        <i class="fas fa-file-contract fa-3x"></i>
                        <h3>No Contracts Found</h3>
                        <p>You don't have any contracts yet. Start by creating a new contract.</p>
                    </div>

                    <!-- Contracts will be dynamically loaded here by JavaScript -->
                </div>

                '''

# Perform the replacement
new_content = re.sub(pattern, replacement, content, flags=re.DOTALL)

# Write the new content
with open(r'c:\xampp\htdocs\2nd-Year-Group-Project\FixLanka\views\company\contracts.php', 'w', encoding='utf-8') as f:
    f.write(new_content)

print("Successfully removed hardcoded contract cards and added dynamic loading structure")
