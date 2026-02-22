import sys

def read_file(file_path):
    encodings = ['utf-8', 'utf-16le', 'utf-16', 'cp1252', 'latin1']
    for enc in encodings:
        try:
            with open(file_path, 'r', encoding=enc) as f:
                content = f.read()
            
            with open('temp_plan.txt', 'w', encoding='utf-8') as out:
                out.write(f"--- SUCCESSFULLY READ WITH {enc} ---\n")
                out.write(content)
            print("Successfully written to temp_plan.txt")
            return
        except Exception as e:
            continue
    print("Failed to read file with any encoding.")

if __name__ == "__main__":
    if len(sys.argv) > 1:
        read_file(sys.argv[1])
