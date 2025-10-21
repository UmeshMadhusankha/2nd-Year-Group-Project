# Upload Directory

This directory stores uploaded user files:
- Profile pictures (users and repairers)
- Company logos
- Other user-uploaded images

## Security Notes
- All uploads are validated for file type (images only)
- Unique filenames are generated to prevent overwrites
- Files are stored with timestamp to avoid conflicts

## Allowed File Types
- JPEG (.jpg, .jpeg)
- PNG (.png)
- GIF (.gif)

## Directory Structure
All files are stored directly in this uploads folder with the naming pattern:
`{unique_id}_{timestamp}.{extension}`

Example: `5f9c8b3a_1634567890.jpg`
