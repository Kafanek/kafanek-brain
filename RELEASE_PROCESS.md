# Release Process

## Creating a New Release on GitHub

To create a new release for Kafánek Brain, follow these steps:

### 1. Prepare Release Notes
- Create or update the release notes file (e.g., `RELEASE_NOTES_v1.2.2.md`)
- Include all new features, improvements, and bug fixes

### 2. Create Release Package
- Build the distribution package (e.g., `kafankuv-mozek-v1.2.2-DESIGN-STUDIO.zip`)
- Ensure all necessary files are included in the package

### 3. Create GitHub Release

1. Navigate to the repository on GitHub: https://github.com/Kafanek/kafanek-brain
2. Click on **Releases** in the right sidebar
3. Click **Create a new release** (or **Draft a new release**)
4. Fill in the release details:
   - **Tag version**: Enter the tag (e.g., `v1.2.2`)
   - **Release title**: Enter the title (e.g., `Kafánek Brain v1.2.2 - AI Design Studio`)
   - **Description**: Copy the content from the release notes file (e.g., from `RELEASE_NOTES_v1.2.2.md`)
   - **Attach binaries**: Upload the distribution package (e.g., `kafankuv-mozek-v1.2.2-DESIGN-STUDIO.zip`)
5. Click **Publish release** ✅

### Example: v1.2.2 Release

- **Tag**: `v1.2.2`
- **Title**: `Kafánek Brain v1.2.2 - AI Design Studio`
- **Description**: Copy from `RELEASE_NOTES_v1.2.2.md`
- **Attach**: `kafankuv-mozek-v1.2.2-DESIGN-STUDIO.zip`

## Release Checklist

- [ ] Release notes file created/updated
- [ ] Distribution package built
- [ ] Tag version determined
- [ ] Release title defined
- [ ] Release description prepared
- [ ] Binaries attached
- [ ] Release published on GitHub

## Notes

- Always use semantic versioning (MAJOR.MINOR.PATCH)
- Test the distribution package before releasing
- Announce the release to users and contributors
- Update documentation if needed
