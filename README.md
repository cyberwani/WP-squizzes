WP Squizzes
===========

This is a repo for a Wordpress Plugin to create awesome quizzes. Built on top of Backbone.js.

##The plugin
The objective of this plugin is to help in the task of create a quiz system for wordpress. This is a explanatory statement, so the concept will work like this:

- There will be a CPT for questions
- Inside a Question edit screen, Backbone helps to dynamically create the answers (and whatever other properties needed)
- Idea #1: create another screen where the user can create a quiz (maybe with a implementation based on shortcodes), using again Backbone to search and add questions to it, or
- Idea 2: the quiz is dynamically generated on the frontend based on a "query" (like, insert questions based on taxonomy, randomize its order, etc).

##Why another quiz plugin?

The answer is simple: after search and test the majority of the available plugins for a client project, I found that no one fits the needs. So, I decided to use the skills that I'm currently learning on Backbone to build the right tool and, at the same time, improve my learning workflow!  

##A note about this repo

This is a beta plugin, so it's not ready yet for publishing. But if you want to contribute, you are welcome!

##Changelog

-**1.0.0**: Initial state of the plugin, with basic functionality and a system to generate random quizzes based on question types.

##TODO

* [ ] Expand functionality for the shortcode
* [ ] Build a shortcode generator using Backbone to build the UI
* [ ] Improve plugin options (like the Results page)