<template>
  <section class="item-container ui-sortable-handle" :class="{ preview: preview }" :data-id="this.$attrs.id">
    <div class="item" :style="'background-color: ' + bgColor + '; color: ' + textColor + ''">
      <img class="app-icon" :src="appIcon" />
      <div class="details">
        <div class="title white">{{ application.title }}</div>
        <!--<div v-if="application.config.enhancedType !== 'disabled'" class="livestats-container white">
          <ul class="livestats">
            <li v-if="application.config.stat1.name">
              <span class="title">{{ application.config.stat1.name }}</span>
              <strong>{{ this.stat1value }}</strong>
            </li>
            <li v-if="application.config.stat2.name">
              <span class="title">{{ application.config.stat2.name }}</span>
              <strong>{{ this.stat2value }}</strong>
            </li>
          </ul>
        </div>-->
      </div>
      <a :style="'color: ' + textColor" class="link white" :href="application.url" v-bind:target="application.link_tab === 'default' ? settings.default_link_tab : application.link_tab">
        <svg class="svg-inline--fa fa-arrow-alt-to-right fa-w-14" aria-hidden="true" data-prefix="fas" data-icon="arrow-alt-to-right" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512" data-fa-i2svg="">
          <path fill="currentColor" d="M448 88v336c0 13.3-10.7 24-24 24h-24c-13.3 0-24-10.7-24-24V88c0-13.3 10.7-24 24-24h24c13.3 0 24 10.7 24 24zM24 320h136v87.7c0 17.8 21.5 26.7 34.1 14.1l152.2-152.2c7.5-7.5 7.5-19.8 0-27.3L194.1 90.1c-12.6-12.6-34.1-3.7-34.1 14.1V192H24c-13.3 0-24 10.7-24 24v80c0 13.3 10.7 24 24 24z"></path>
        </svg>
        <!-- <i class="fas fa-arrow-alt-to-right"></i> -->
      </a>
      <div v-if="application.description !== ''" content-class="tooltip-content" max-width="500px" anchor="top middle" self="bottom middle">{{ application.description }}</div>
      <!--<div v-if="application.config.enhancedType !== 'disabled'" @click="refreshData" class="tile-actions refresh">
        <q-icon class="" name="refresh"></q-icon>
        {{ $t('refresh_stats') }}
      </div>-->
    </div>
  </section>
</template>

<script>
import EnhancedApps from '../plugins/EnhancedApps'
import _ from 'lodash'
export default {
  name: 'Tile',

  props: ['application', 'stat1valueinit', 'stat2valueinit'],

  components: {},

  computed: {
    textColor() {
      return '#000000'
      const bgColor = this.bgColor
      const lightColor = '#ffffff'
      const darkColor = '#000000'
      const color = bgColor.charAt(0) === '#' ? bgColor.substring(1, 7) : bgColor
      const alpha = bgColor.charAt(0) === '#' ? bgColor.substring(7, 9) : bgColor.substring(6, 8)
      const r = parseInt(color.substring(0, 2), 16) // hexToR
      const g = parseInt(color.substring(2, 4), 16) // hexToG
      const b = parseInt(color.substring(4, 6), 16) // hexToB
      const a = parseFloat(parseInt((parseInt(alpha, 16) / 255) * 1000) / 1000)
      const brightness = r * 0.299 + g * 0.587 + b * 0.114 + (1 - a) * 255
      return brightness > 186 ? darkColor : lightColor
    },
    bgColor() {
      if (this.application.color !== 'null' && this.application.color !== null) {
        return this.application.color
      }
      return '#222222'
    },
    preview() {
      return this.application.preview
    },
    running() {
      // return this.$store.state.tiles.running
    },
    settings() {
      // return this.$store.state.app.settings
    },
    appIcon() {
      return '';
    }
  },

  watch: {
    application: function (newdata, olddata) {
      clearTimeout(this.check)
      if (newdata.config.enhancedType !== 'disabled') {
        // this.checkVisible()
      }
    },
    running: function (newdata, olddata) {
      if (newdata === false) {
        clearTimeout(this.check)
      }
      if (olddata === false && newdata === true) {
        this.timedChecks()
      }
    },
    stat1valueinit: function (newdata, olddata) {
      this.stat1value = newdata
    },
    stat2valueinit: function (newdata, olddata) {
      this.stat2value = newdata
    }
  },

  asyncComputed: {
    /*async appIcon() {
      if (this.application.icon === null) {
        return '/heimdall-logo-white.svg'
      }
      // Is a file upload
      if (typeof this.application.icon === 'object') {
        // work out how to preview the image
        console.log(this.application.icon)
      }
      // Is an image stored online
      if (this.application.icon && this.application.icon.startsWith('http')) {
      }
      return this.application.icon
    }*/
  },

  data() {
    return {
      icon: this.$attrs.icon || '/heimdall-logo-white.svg',
      stat1value: this.stat1valueinit || null,
      stat2value: this.stat2valueinit || null,
      check: null,
      active: 2000,
      maxTimer: 45000,
      timer: 5000
    }
  },
  mounted() {
    this.refreshData()
  },
  methods: {
    async timedChecks() {
      const current1 = this.stat1value
      const current2 = this.stat2value
      const data = await this.checkForData()
      if (!data) {
        return
      }
      if (data.stat1 !== current1 && this.application.config.stat1.updateOnChange === 'Yes') {
        this.timer = this.active
      } else if (data.stat2 !== current2 && this.application.config.stat2.updateOnChange === 'Yes') {
        // There has been a change to the data
        this.timer = this.active
      } else {
        if (this.timer < this.maxTimer) this.timer += 5000
      }
      this.stat1value = data.stat1
      this.stat2value = data.stat2
      clearTimeout(this.check) // Make sure timer is cleared, it should be, but shouldn't hurt to make sure
      if (
        // check if update on change is set on at least 1 stat
        this.application.config.stat1.updateOnChange === 'Yes' ||
        this.application.config.stat2.updateOnChange === 'Yes'
      ) {
        console.log('timer: ' + this.timer)
        this.check = setTimeout(this.timedChecks, this.timer)
      }
    },

    async refreshData() {
      await this.timedChecks()
      this.$q.notify({
        message: this.$t('updated'),
        type: 'positive',
        progress: true,
        position: 'bottom',
        timeout: 1500
      })
    },

    forceCheck() {
      clearTimeout(this.check)
      this.checkForData()
    },

    async checkForData() {
      if (this.application.config.enhancedType && this.application.config.enhancedType !== 'disabled') {
        const enhanced = new EnhancedApps(this.application)
        let call
        try {
          call = await enhanced.call()
        } catch (e) {
          console.error(e)
        }
        if (!call) {
          return
        }
        const stat1 = this.application.config.stat1.key !== null && this.application.config.stat1.key !== '' ? _.get(call.data.result.stat1, this.application.config.stat1.key, null) : call.data.result.stat1
        const stat2 = this.application.config.stat2.key !== null && this.application.config.stat2.key !== '' ? _.get(call.data.result.stat2, this.application.config.stat2.key, null) : call.data.result.stat2
        return {
          stat1: enhanced.filter(stat1, this.application.config.stat1),
          stat2: enhanced.filter(stat2, this.application.config.stat2)
        }
      }
    }
  }
}
</script>
