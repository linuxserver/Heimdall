<template>
  <div class="flex flex-center">
    <div class="noapps" v-if="apps.length === 0">
      {{ $t('no_apps') }}
      <button style="margin-top: 20px" unelevated color="cyan-8" to="/account">
        {{ $t('manage_apps') }}
      </button>
    </div>
    <Tile v-else v-for="application in apps" :key="application.id" v-bind="application" :application="application" />
  </div>
</template>

<script>
import Tile from '../components/Tile'

export default {
  name: 'PageIndex',

  props: ['apps', 'searchfilter'],

  components: {
    Tile
  },

  computed: {
    /*applications: function () {
      let tiles = null
      if (this.filter === null) {
        tiles = this.$store.state.tiles.active
      } else {
        console.log('active')
        console.log(this.$store.state.tiles.active)
        const notNull = this.$store.state.tiles.active.filter(a => a.tags !== null)
        console.log('notnull')
        console.log(notNull)
        tiles = notNull.filter(a => a.tags.find(name => name === this.filter))
      }
      // console.log(tiles)
      if (this.searchfilter === null) {
        return tiles
      } else {
        return tiles.filter(v => v.title.toLowerCase().indexOf(this.searchfilter) > -1)
      }
    }*/
  },

  data() {
    return {}
  },
  mounted() {
    console.log(apps)
  }
}
</script>
