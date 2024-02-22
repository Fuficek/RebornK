---
title: Kontakt
media_order: 'instagram.png,facebook.png,kessner.png'


form:
    name: contact

    fields:
        name:
          label: Jméno a příjmení
          placeholder: Jméno a příjmení
          autocomplete: on
          type: text
          validate:
            required: true

        email:
          label: Email
          placeholder: Zadejte Váš Email
          type: email
          validate:
            required: true

        message:
          label: Zpráva
          placeholder: Zde napište svou zprávu.
          type: textarea
          validate:
            required: true

    buttons:
        submit:
          type: submit
          value: Odeslat
    process:
        - save:
            fileprefix: feedback-
            dateformat: Ymd-His-u
            extension: txt
            body: "{% include 'forms/data.txt.twig' %}"
        - email:
            from: "{{ config.plugins.email.from }}"
            to: "{{ config.plugins.email.to }}"
            subject: "Nové vyplnění formuláře na webu od: {{ form.value.name|e }}"
            body: "{% include 'forms/data.html.twig' %}"
            template: "email/base.html.twig"
        - message: 'Děkujeme za vyplnění formuláře. Brzy se Vám ozveme.'
---

