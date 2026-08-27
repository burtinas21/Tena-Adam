<script setup>
import LandingNavbar from "../../components/landing/LandingNavbar.vue";
import HeroSection from "../../components/landing/HeroSection.vue";
import ServicesSection from "../../components/landing/ServicesSection.vue";
import FeaturesSection from "../../components/landing/FeaturesSection.vue";
import HowItWorksSection from "../../components/landing/HowItWorksSection.vue";
import AboutSection from "../../components/landing/AboutSection.vue";
import CTASection from "../../components/landing/CTASection.vue";
import ContactSection from "../../components/landing/ContactSection.vue";
import LandingFooter from "../../components/landing/LandingFooter.vue";
</script>

<template>
  <div class="min-h-screen bg-white">
    <LandingNavbar />

    <main>
      <HeroSection />
      <ServicesSection />
      <FeaturesSection />
      <HowItWorksSection />
      <AboutSection />
      <CTASection />
      <ContactSection />
    </main>
    <LandingFooter />
  </div>
</template>
