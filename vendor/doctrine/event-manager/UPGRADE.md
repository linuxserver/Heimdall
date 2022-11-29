# Upgrade to 1.2

## Deprecated calling `EventManager::getListeners()` without an event name

When calling `EventManager::getListeners()` without an event name, all
listeners were returned, keyed by event name. A new method `getAllListeners()`
has been added to provide this functionality. It should be used instead.
