<template>
  <div class="min-h-screen bg-[#F5F7FA] pb-nav">

    <!-- Header -->
    <div class="relative bg-gradient-to-br from-brand-700 to-brand-800 px-5 pt-12 pb-20 overflow-hidden">
      <div class="absolute -top-6 -right-6 w-32 h-32 rounded-full bg-white/5" />
      <div class="absolute bottom-2 left-4 w-16 h-16 rounded-full bg-white/5" />
      <div class="relative">
        <p class="text-white/60 text-xs font-medium tracking-wide">Info & Pengumuman</p>
        <h1 class="text-white font-bold text-xl mt-0.5">Berita Pesantren</h1>
      </div>
    </div>

    <!-- Content -->
    <div class="relative z-10 px-4 -mt-12 flex flex-col gap-3">
      <template v-if="loading">
        <div v-for="i in 4" :key="i"
             class="bg-white rounded-2xl p-4 shadow-sm border border-gray-100">
          <div class="skeleton h-3 w-20 rounded mb-3" />
          <div class="skeleton h-5 w-full rounded mb-2" />
          <div class="skeleton h-3 w-4/5 rounded" />
        </div>
      </template>

      <template v-else-if="posts.length">
        <!-- Featured first post -->
        <div v-if="posts[0]"
             class="bg-white rounded-2xl shadow-sm border border-gray-100/80 overflow-hidden
                    active:scale-[0.99] transition-transform">
          <div v-if="posts[0].featured_image"
               class="relative h-44 overflow-hidden">
            <img :src="posts[0].featured_image"
                 class="w-full h-full object-cover"
                 :alt="posts[0].title" />
            <div class="absolute inset-0 bg-gradient-to-t from-black/40 to-transparent" />
            <span class="absolute bottom-3 left-3 text-xs px-2.5 py-1 rounded-full font-semibold text-white"
                  :class="catBg(posts[0].category?.slug)">
              {{ posts[0].category?.name ?? 'Pengumuman' }}
            </span>
          </div>
          <div class="p-4">
            <div v-if="!posts[0].featured_image" class="flex items-center gap-2 mb-2">
              <span class="text-xs px-2.5 py-1 rounded-full font-semibold"
                    :class="catText(posts[0].category?.slug)">
                {{ posts[0].category?.name ?? 'Pengumuman' }}
              </span>
              <span class="text-xs text-gray-400">{{ formatDate(posts[0].published_at) }}</span>
            </div>
            <h3 class="font-bold text-gray-800 text-base leading-snug mb-2">{{ posts[0].title }}</h3>
            <p class="text-sm text-gray-500 leading-relaxed line-clamp-2">{{ posts[0].excerpt }}</p>
            <p v-if="posts[0].featured_image" class="text-xs text-gray-400 mt-3">
              {{ formatDate(posts[0].published_at) }}
            </p>
          </div>
        </div>

        <!-- Rest of posts -->
        <div v-for="post in posts.slice(1)" :key="post.id"
             class="bg-white rounded-2xl shadow-sm border border-gray-100/80
                    active:scale-[0.99] transition-transform flex gap-3 overflow-hidden">
          <img v-if="post.featured_image" :src="post.featured_image"
               class="w-24 h-24 object-cover flex-shrink-0"
               :alt="post.title" />
          <div class="flex-1 min-w-0 p-3.5" :class="post.featured_image ? 'pl-0' : ''">
            <div class="flex items-center gap-1.5 mb-1.5">
              <span class="text-xs px-2 py-0.5 rounded-full font-semibold"
                    :class="catText(post.category?.slug)">
                {{ post.category?.name ?? 'Info' }}
              </span>
              <span class="text-xs text-gray-400">{{ formatDate(post.published_at) }}</span>
            </div>
            <h3 class="font-bold text-gray-800 text-sm leading-snug line-clamp-2">{{ post.title }}</h3>
            <p class="text-xs text-gray-500 mt-1 line-clamp-2">{{ post.excerpt }}</p>
          </div>
        </div>

        <!-- Load more -->
        <button v-if="hasMore" @click="loadMore" :disabled="loadingMore"
                class="w-full py-3.5 text-sm font-semibold text-brand-700 bg-white
                       rounded-2xl border border-brand-100 shadow-sm
                       active:bg-brand-50 transition-colors">
          {{ loadingMore ? 'Memuat...' : 'Muat lebih banyak' }}
        </button>
      </template>

      <div v-else-if="!loading"
           class="text-center py-16 text-gray-400 text-sm bg-white rounded-2xl border border-gray-100">
        <span class="text-4xl block mb-2">📭</span>
        Belum ada pengumuman
      </div>
    </div>

  </div>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue'
import { api } from '@/api/client'

interface Post {
  id: number; title: string; slug: string; excerpt: string
  featured_image: string | null; published_at: string
  category: { slug: string; name: string } | null
}

const loading     = ref(true)
const loadingMore = ref(false)
const posts       = ref<Post[]>([])
const page        = ref(1)
const hasMore     = ref(false)

function catText(slug?: string) {
  if (!slug) return 'bg-gray-100 text-gray-600'
  if (slug === 'achievement')  return 'bg-amber-50 text-amber-700'
  if (slug === 'announcement') return 'bg-sky-50 text-sky-700'
  return 'bg-brand-50 text-brand-700'
}
function catBg(slug?: string) {
  if (!slug) return 'bg-gray-600/80'
  if (slug === 'achievement')  return 'bg-amber-500/80'
  if (slug === 'announcement') return 'bg-sky-500/80'
  return 'bg-brand-700/80'
}
function formatDate(d: string) {
  return new Date(d).toLocaleDateString('id-ID', { day: 'numeric', month: 'long', year: 'numeric' })
}

async function fetchPosts(p = 1) {
  const res = await api.get('/posts', { params: { limit: 10, page: p } })
  return res.data
}
async function loadMore() {
  loadingMore.value = true; page.value++
  try {
    const data = await fetchPosts(page.value)
    posts.value.push(...(data.data ?? data))
    hasMore.value = !!(data.next_page_url)
  } finally { loadingMore.value = false }
}
onMounted(async () => {
  try {
    const data = await fetchPosts(1)
    posts.value = data.data ?? data
    hasMore.value = !!(data.next_page_url ?? false)
  } finally { loading.value = false }
})
</script>
