# Changelog
All notable changes to this extension will be documented in this file.

## [14.0.0] - 2026-08-17
### Added
- Added support for TYPO3 14 while retaining TYPO3 13 compatibility.

### Changed
- Replaced the file list controller XCLASS with a PSR-14 button bar event listener.
- Updated backend module registration for the renamed Media module in TYPO3 14.
- Updated module access and Fluid infobox severity configuration for TYPO3 14.
- Replaced deprecated button and Extbase JSON view usage.
- Read chunk parameters from the PSR-7 request instead of `$_REQUEST`.
- Added Composer CI scripts, PHPStan level-8 analysis, TYPO3 coding standards, and a strict pre-commit hook.

## [13.0.0] - 2025-08-16
### Added
- Added release for TYPO3 version 13.
