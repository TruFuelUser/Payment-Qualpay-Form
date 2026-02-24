# 📝 Lista de tareas pendientes – TruFuel Contact Plugin

## ✅ Lo que ya tienes
- [x] Todas las clases en `includes/` (Errors, Rest, ContactController, etc.)
- [x] Archivos `messages-xx.json` multilenguaje en `/messages/`
- [x] Archivo del formulario en `public/partials/form.php`
- [x] Archivos CSS y JS separados en `public/css` y `public/js`

---

## 🔜 Lo que falta

### 1. reCAPTCHA
- [ ] Crear **clase `TRF_Recaptcha`** en `includes/class-recaptcha.php`
  - Método para obtener site key y secret key (desde opciones o constantes).
  - Método `verify()` para validar tokens contra Google.
- [ ] Integrar la verificación en tu endpoint REST (`class-rest.php`).
- [ ] Inyectar **site key** al JS (con `wp_localize_script`).
- [ ] Actualizar `public.js` para generar y enviar el token reCAPTCHA al backend.

### 2. Multilenguaje en formulario
- [ ] Crear **helper de idioma** (`helpers-i18n.php`) → usar `pll_current_language()` si Polylang está activo, `get_locale()` como fallback.
- [ ] Definir etiquetas de los campos (`Nombre`, `Apellido`, etc.) por idioma.
- [ ] Reemplazar placeholders/textos en `form.php` con esas etiquetas dinámicas.
- [ ] (Opcional) migrar textos de interfaz a `__()`/`_e()` para compatibilidad con `.po/.mo`.

### 3. Archivo principal del plugin
- [ ] Crear `trufuel-payment.php` en la raíz con cabecera estándar de WordPress.
- [ ] Definir constantes (`TRF_PAYMENT_PATH`, `TRF_CONTACT_URL`, `TRF_CONTACT_VER`).
- [ ] Incluir todas las clases y helpers (`require_once`).
- [ ] Registrar hooks:
  - `wp_enqueue_scripts` → encolar `public.css` y `public.js`.
  - `admin_enqueue_scripts` → encolar `admin.css` y `admin.js` en tu página.
  - `init` → registrar shortcode `[trufuel_contact_form]`.
  - `rest_api_init` → registrar rutas REST.
  - `admin_menu` / `admin_init` → registrar ajustes de admin.

### 4. Shortcode
- [ ] Registrar `[trufuel_contact_form]` → debe renderizar `public/partials/form.php`.
- [ ] Probar que el formulario aparece al usar el shortcode en una página/post.

### 5. Admin (panel de ajustes)
- [ ] Crear `admin/partials/settings-page.php` (formulario de opciones).
- [ ] Registrar opciones:
  - Ruta de `keys.json`.
  - reCAPTCHA site key.
  - reCAPTCHA secret key.
- [ ] Sanitizar/validar opciones (`add_settings_error` en caso de error).
- [ ] Mostrar formulario en menú de admin (`TruFuel Contact`).
- [ ] Encolar `admin.css` y `admin.js` solo en esa página.

---

## 🧪 Después de completar lo anterior
- [ ] Probar flujo completo de envío de formulario con reCAPTCHA válido.
- [ ] Probar validaciones (campos faltantes, formatos inválidos).
- [ ] Verificar mensajes multilenguaje desde `messages-xx.json`.
- [ ] Revisar códigos HTTP devueltos (201, 400, 422, 500, 503).
- [ ] Ajustar estilos (`public.css`) y accesibilidad (labels, aria).
- [ ] Documentar: instalación, uso del shortcode, configuración de opciones, endpoints REST.
