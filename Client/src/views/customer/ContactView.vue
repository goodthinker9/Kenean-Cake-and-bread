<template>
  <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10 space-y-12 font-sans">
    <!-- Header -->
    <div class="text-center space-y-3 max-w-2xl mx-auto">
      <div class="inline-flex items-center space-x-1.5 px-3 py-1 bg-[#FFF1F2] border border-[#FECDD3] text-[#E11D48] text-xs font-bold rounded-full">
        <span>✉️</span>
        <span>We're Here For You</span>
      </div>
      <h1 class="text-3xl sm:text-5xl font-serif font-black text-[#4A1521]">
        Contact Us
      </h1>
      <p class="text-xs sm:text-sm text-[#7A3644] leading-relaxed">
        Have questions about custom cake orders, bulk corporate catering, or today's fresh oven batches? Reach out to our bakers anytime!
      </p>
    </div>

    <!-- 3 Quick Contact Info Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
      <div class="bg-white rounded-3xl border border-[#F3E8E8] p-6 text-center space-y-2 shadow-2xs hover:shadow-md transition">
        <div class="w-12 h-12 rounded-2xl bg-[#FFF1F2] text-[#E11D48] flex items-center justify-center mx-auto text-2xl shadow-2xs">
          📞
        </div>
        <h3 class="font-serif font-bold text-base text-[#38121A]">Phone Support</h3>
        <p class="text-xs text-[#7A6B60]">Direct branch customer helpline</p>
        <div class="font-bold text-xs text-[#881337] pt-1">
          <div>+251 911 123 456</div>
          <div>+251 11 661 1111</div>
        </div>
      </div>

      <div class="bg-white rounded-3xl border border-[#F3E8E8] p-6 text-center space-y-2 shadow-2xs hover:shadow-md transition">
        <div class="w-12 h-12 rounded-2xl bg-[#FFF1F2] text-[#E11D48] flex items-center justify-center mx-auto text-2xl shadow-2xs">
          ✉️
        </div>
        <h3 class="font-serif font-bold text-base text-[#38121A]">Email Us</h3>
        <p class="text-xs text-[#7A6B60]">For quotes and custom designs</p>
        <div class="font-bold text-xs text-[#881337] pt-1">
          <div>hello@sweetbakes.com</div>
          <div>orders@sweetbakes.com</div>
        </div>
      </div>

      <div class="bg-white rounded-3xl border border-[#F3E8E8] p-6 text-center space-y-2 shadow-2xs hover:shadow-md transition">
        <div class="w-12 h-12 rounded-2xl bg-[#FFF1F2] text-[#E11D48] flex items-center justify-center mx-auto text-2xl shadow-2xs">
          ⏰
        </div>
        <h3 class="font-serif font-bold text-base text-[#38121A]">Baking Hours</h3>
        <p class="text-xs text-[#7A6B60]">Fresh bread baked twice daily</p>
        <div class="font-bold text-xs text-[#881337] pt-1">
          <div>Mon - Sun: 7:00 AM - 9:00 PM</div>
          <div class="text-[11px] text-[#059669]">Ovens open 7 days a week</div>
        </div>
      </div>
    </div>

    <!-- Main Grid: Message Form (7 cols) & Bakery Locations (5 cols) -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
      <!-- Contact Form (7 cols) -->
      <div class="lg:col-span-7 bg-white rounded-3xl border border-[#F3E8E8] p-6 sm:p-8 space-y-6 shadow-2xs">
        <h2 class="text-xl font-serif font-bold text-[#38121A] border-b border-[#F8E7EA] pb-3">
          Send Us a Message
        </h2>

        <form @submit.prevent="sendMessage" class="space-y-4">
          <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
              <label class="block text-xs font-bold uppercase tracking-wider text-[#38121A] mb-1">Your Name</label>
              <input
                v-model="name"
                type="text"
                placeholder="Abebe Kebede"
                class="w-full bg-[#FAF7F5] border border-[#F2D7DC] rounded-xl px-4 py-2.5 text-xs text-[#261814] focus:ring-1 focus:ring-[#E11D48]"
                required
              />
            </div>
            <div>
              <label class="block text-xs font-bold uppercase tracking-wider text-[#38121A] mb-1">Email Address</label>
              <input
                v-model="email"
                type="email"
                placeholder="abebe@example.com"
                class="w-full bg-[#FAF7F5] border border-[#F2D7DC] rounded-xl px-4 py-2.5 text-xs text-[#261814] focus:ring-1 focus:ring-[#E11D48]"
                required
              />
            </div>
          </div>

          <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
              <label class="block text-xs font-bold uppercase tracking-wider text-[#38121A] mb-1">Phone Number</label>
              <input
                v-model="phone"
                type="text"
                placeholder="+251 9XX XXX XXX"
                class="w-full bg-[#FAF7F5] border border-[#F2D7DC] rounded-xl px-4 py-2.5 text-xs text-[#261814] focus:ring-1 focus:ring-[#E11D48]"
              />
            </div>
            <div>
              <label class="block text-xs font-bold uppercase tracking-wider text-[#38121A] mb-1">Subject</label>
              <select
                v-model="subject"
                class="w-full bg-[#FAF7F5] border border-[#F2D7DC] rounded-xl px-4 py-2.5 text-xs text-[#261814] focus:ring-1 focus:ring-[#E11D48]"
              >
                <option value="General Inquiry">General Inquiry</option>
                <option value="Custom Cake Quotation">Custom Cake Quotation</option>
                <option value="Delivery Question">Delivery Question</option>
                <option value="Corporate / Event Catering">Corporate / Event Catering</option>
                <option value="Feedback & Suggestions">Feedback &amp; Suggestions</option>
              </select>
            </div>
          </div>

          <div>
            <label class="block text-xs font-bold uppercase tracking-wider text-[#38121A] mb-1">Message</label>
            <textarea
              v-model="message"
              rows="4"
              placeholder="How can our bakery team assist you today?"
              class="w-full bg-[#FAF7F5] border border-[#F2D7DC] rounded-xl p-3 text-xs text-[#261814] focus:ring-1 focus:ring-[#E11D48]"
              required
            ></textarea>
          </div>

          <button
            type="submit"
            :disabled="sent"
            class="px-8 py-3.5 bg-[#E11D48] hover:bg-[#BE123C] text-white text-xs font-bold uppercase tracking-wider rounded-2xl shadow-md hover:shadow-lg transition active:scale-98"
          >
            {{ sent ? 'Message Sent! ✓' : 'Send Message →' }}
          </button>
        </form>
      </div>

      <!-- Bakery Locations & Map (5 cols) -->
      <div class="lg:col-span-5 bg-white rounded-3xl border border-[#F3E8E8] p-6 sm:p-8 space-y-6 shadow-2xs">
        <h2 class="text-xl font-serif font-bold text-[#38121A] border-b border-[#F8E7EA] pb-3">
          Our Bakery Locations
        </h2>

        <div class="space-y-4 text-xs">
          <div class="p-4 bg-[#FAF7F5] rounded-2xl border border-[#F2D7DC] space-y-1">
            <div class="flex items-center justify-between">
              <span class="font-bold text-sm text-[#38121A]">Bole Branch</span>
              <span class="text-[10px] bg-[#ECFDF5] text-[#059669] font-bold px-2 py-0.5 rounded-full">Open Now</span>
            </div>
            <p class="text-[#7A6B60]">Bole Medhanialem, Edna Mall Area, Addis Ababa</p>
            <p class="text-[#881337] font-semibold">+251 11 661 1111</p>
          </div>

          <div class="p-4 bg-[#FAF7F5] rounded-2xl border border-[#F2D7DC] space-y-1">
            <div class="flex items-center justify-between">
              <span class="font-bold text-sm text-[#38121A]">Megenagna Branch</span>
              <span class="text-[10px] bg-[#ECFDF5] text-[#059669] font-bold px-2 py-0.5 rounded-full">Open Now</span>
            </div>
            <p class="text-[#7A6B60]">Near Megenagna Square, Zefmesh Mall Road</p>
            <p class="text-[#881337] font-semibold">+251 11 662 2222</p>
          </div>

          <div class="p-4 bg-[#FAF7F5] rounded-2xl border border-[#F2D7DC] space-y-1">
            <div class="flex items-center justify-between">
              <span class="font-bold text-sm text-[#38121A]">Piassa Branch</span>
              <span class="text-[10px] bg-[#ECFDF5] text-[#059669] font-bold px-2 py-0.5 rounded-full">Open Now</span>
            </div>
            <p class="text-[#7A6B60]">Churchill Avenue, Historic Quarter</p>
            <p class="text-[#881337] font-semibold">+251 11 155 3333</p>
          </div>
        </div>

        <div class="p-4 bg-[#FFF1F2] rounded-2xl border border-[#FECDD3] text-xs text-[#881337]">
          <strong>Fresh Bread Timing:</strong> Morning batch ready at 7:30 AM; afternoon warm loaves ready at 3:30 PM daily.
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue';
import { appConfig } from '../../config';
import { toast } from '../../services/feedback';

const name = ref('');
const email = ref('');
const phone = ref('');
const subject = ref('General Inquiry');
const message = ref('');
const sent = ref(false);

function sendMessage() {
  sent.value = true;
  toast.success(`Thank you for contacting ${appConfig.brandName}! Our team will respond within 24 hours.`);
  name.value = '';
  email.value = '';
  phone.value = '';
  message.value = '';
  setTimeout(() => {
    sent.value = false;
  }, 4000);
}
</script>
