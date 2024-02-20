---
title: Kontakt
media_order: 'instagram.png,facebook.png,kessner.png'


form:
    name: contact

    fields:
        name:
          label: Name
          placeholder: Enter your name
          autocomplete: on
          type: text
          validate:
            required: true

        email:
          label: Email
          placeholder: Enter your email address
          type: email
          validate:
            required: true

        message:
          label: Message
          placeholder: Enter your message
          type: textarea
          validate:
            required: true

    buttons:
        submit:
          type: submit
          value: Submit
    process:
        - save:
            fileprefix: feedback-
            dateformat: Ymd-His-u
            extension: txt
            body: "{% include 'forms/data.txt.twig' %}"
        - email:
            from: "{{ config.plugins.email.from }}"
            to: "{{ config.plugins.email.to }}"
            subject: "Contact by {{ form.value.name|e }}"
            body: "{% include 'forms/data.html.twig' %}"
            template: "email/base.html.twig"
---

