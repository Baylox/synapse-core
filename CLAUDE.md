# Project conventions for Claude

## Commits
- **Author commits as the user, never as Claude.** Use the git identity
  `Baylox <microsoftbaylo@laposte.net>` (run `git config user.name "Baylox"` and
  `git config user.email "microsoftbaylo@laposte.net"` at the start of a session,
  since the environment is recreated each time).
- **Do not add** `Co-Authored-By: Claude ...` or `Claude-Session: ...` trailers
  to commit messages.
