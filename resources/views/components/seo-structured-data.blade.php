{{-- JSON-LD Structured Data for SEO --}}
<script type="application/ld+json">
{
  "@context": "https://schema.org",
  "@type": "E-commerce",
  "name": "{{ setting('brand_name', 'NITIP DI END') }}",
  "description": "{{ setting('meta_description', 'Titipan spesial dengan harga terbaik') }}",
  "url": "{{ config('app.url') }}",
  "logo": "{{ asset('images/icons/icon-512.png') }}",
  "sameAs": [
    "{{ setting('social_facebook', '#') }}",
    "{{ setting('social_instagram', '#') }}",
    "{{ setting('social_whatsapp', '#') }}"
  ],
  "contactPoint": {
    "@type": "ContactPoint",
    "contactType": "Customer Service",
    "telephone": "{{ setting('whatsapp', '6285123456789') }}",
    "availableLanguage": ["id", "en"]
  },
  "address": {
    "@type": "PostalAddress",
    "addressCountry": "ID"
  }
}
</script>

{{-- Organization Schema --}}
<script type="application/ld+json">
{
  "@context": "https://schema.org",
  "@type": "Organization",
  "name": "{{ setting('brand_name', 'NITIP DI END') }}",
  "url": "{{ config('app.url') }}",
  "logo": "{{ asset('images/icons/icon-512.png') }}",
  "description": "{{ setting('meta_description', 'Titipan spesial dengan harga terbaik') }}",
  "founder": "{{ setting('brand_owner', 'Nabila Adriyana') }}"
}
</script>
