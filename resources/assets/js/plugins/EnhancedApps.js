import axios from 'axios'
// import { i18n } from 'boot/i18n.js'
export default class EnhancedApps {
  constructor(application) {
    this.data = application.config
    this.id = application.id
  }

  async call() {
    const request = await axios.get('enhanced/' + this.id)
    return request
  }

  async test() {
    try {
      // console.log(this.data)
      const test = await axios.post('enhanced/test', this.data)
      /*Notify.create({
        type: 'positive',
        message: i18n.t('api_test_success') + ': ' + JSON.stringify(test.data),
        progress: true,
        position: 'bottom',
        timeout: 1500
      })*/
      return test
    } catch (e) {
      console.log(e.response)
      /*Notify.create({
        type: 'negative',
        message: i18n.t('api_test_failure') + ': ' + JSON.stringify(e.response.data.result),
        progress: true,
        position: 'bottom',
        timeout: 1500
      })*/
    }
  }

  static types() {
    return [
      {
        id: 'disabled',
        value: 'disabled'
      },
      {
        id: 'none',
        value: 'none'
      },
      {
        id: 'apikey',
        value: 'apikey'
      },
      {
        id: 'cookie',
        value: 'cookie'
      },
      {
        id: 'basic_auth',
        value: 'basic_auth'
      }
    ]
  }

  humanFileSize(size) {
    const i = size === 0 ? 0 : Math.floor(Math.log(size) / Math.log(1024))
    return (size / Math.pow(1024, i)).toFixed(2) * 1 + ' ' + ['B', 'kB', 'MB', 'GB', 'TB'][i]
  }

  filter(value, stat) {
    switch (stat.filter) {
      case 'count':
        return value.length
      case 'size':
        return this.humanFileSize(value)
      case 'filter':
        return value.filter(val => {
          return val[stat.filterBy].toString() === stat.filterByValue.toString()
        }).length
    }
    return value
  }

  static filters() {
    return [
      {
        id: 'none',
        value: 'none'
      },
      {
        id: 'size',
        value: 'size'
      },
      {
        id: 'speed',
        value: 'speed'
      },
      {
        id: 'count',
        value: 'count'
      },
      {
        id: 'filter',
        value: 'filter'
      }
    ]
  }

  fields() {
    if (this.data.type === 1) {
      return [{}]
    }
  }
}
