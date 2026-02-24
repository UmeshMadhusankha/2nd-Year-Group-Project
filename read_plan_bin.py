
try:
    with open(r'c:\xampp\htdocs\2nd-Year-Group-Project\FixLanka-Business-Logic-Documentation\IMPLEMENTATION-PLAN.md', 'rb') as f:
        content = f.read()
        print(f"First 100 bytes: {content[:100]}")
        try:
            print(content.decode('utf-8'))
        except:
            print("utf-8 failed, trying utf-16")
            try:
                print(content.decode('utf-16'))
            except:
                print("utf-16 failed")
except Exception as e:
    print(e)
