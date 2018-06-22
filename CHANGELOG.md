# Release Notes

## v1.4.0 (2018-02-18)

### Added
- Tag(folder) support
- Image preview for uploading icons
- A load of supported apps, full list of apps https://github.com/linuxserver/Heimdall/projects/1

### Changed
- Edited vendor/laravelcollective/html/src/FormBuilder.php to allow relative links #3369de9
- Changed links to use relative links for reverse proxy support
- Links open in new tab

### Fixed
- adds all the fixes in the 1.3.x point releases and on master

## v1.3.0 (2018-02-09)

### Added
- guzzlehttp/guzzle as a dependency
- Variable polling, so interval is increased when an app is idle and decreased when it's active
- Turkish language translation
- Added Sabnzbd enhanced application

### Changed
- Updated composer dependencies
- Added live stats to Nzbget supported application
- Changed Pihole to an enhanced application
- Changed NZBGet to an enhanced application

### Fixed
- Fixed autocomplete being hard to see
- Fixed checkboxes not working on edge


## v1.2.0 (2018-02-07)

### Added
- Translation support
- Initial "Supported" application support
- Finnish translation
- Swedish translation
- German translation
- French translation
- Spanish translation
- Duplicati supported application
- Emby supported application
- Nzbget supported application
- Pfsense supported application
- Pihole supported application
- Plex supported application
- Portainer supported application
- Unifi supported application

### Changed
- button layout and behaviour

### Fixed
- Bottom of button too short in some browsers
- Icon not loading back in when required fields not filled in


## v1.1.0 (2018-02-05)

### Added
- Ability to change background
- Settings section
- Update procedure
- Google/DuckDuckGo/Bing search from homepage
- Added edit button to tile page

### Changed
- Icon used to put tiles into config mode
