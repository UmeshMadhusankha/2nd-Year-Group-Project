try:
    with open(r'c:\xampp\htdocs\2nd-Year-Group-Project\FixLanka-Business-Logic-Documentation\IMPLEMENTATION-PLAN.md', 'r', encoding='utf-16') as f:
        content = f.read()
    with open(r'c:\xampp\htdocs\2nd-Year-Group-Project\plan_content.txt', 'w', encoding='utf-8') as f:
        f.write(content)
    print("Successfully wrote plan content to plan_content.txt")
except Exception as e:
    print(f"Error: {e}")
