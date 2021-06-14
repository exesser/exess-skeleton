# Observers #

As we know the Digital Workplace frontend is highly dynamic.
The backend is in charge of things such as where you want to go when you click a menu button,
what needs to happen when you click an action button, where you go after you submit a form, 
whether or not you want to display a progress indicator in a given guidance, etc.

Due to this dynamic nature, it is not possible to hardcode &-bindings into our Angular components.
Yet, we need certain components to communicate with others. The way we do this is by using observers.
This allows two components to know nothing about each other and still communicate at runtime.
For example, if you have a component A and a component B as such, they can use the observer to invoke callbacks
on each other without ever knowing what the other component is:

    +----------------+                  +----------------+                  +----------------+
    |                |                  |                |                  |                |
    |                | +--------------> |                | +--------------> |                |
    |  Component A   |                  |    Observer    |                  |  Component B   |
    |                | <--------------+ |                | <--------------+ |                |
    |                |                  |                |                  |                |
    +----------------+                  +----------------+                  +----------------+
