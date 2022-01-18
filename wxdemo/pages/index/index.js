// index.js
// 获取应用实例
const app = getApp()
let token = ''
Page({
  data: {
    userInfo: {},
    hasUserInfo: false,
    canIUseGetUserProfile: false,
    canIUseOpenData: wx.canIUse('open-data.type.userAvatarUrl') && wx.canIUse('open-data.type.userNickName') // 如需尝试获取用户信息可改为false
  },
  // 事件处理函数
  bindViewTap() {
    wx.navigateTo({
      url: '../logs/logs'
    })
  },
  onLoad() {
    if (wx.getUserProfile) {
      this.setData({
        canIUseGetUserProfile: true
      })
    }
  },
  getWxLoginCode() {
    return new Promise((resolve, reject) => {
      // 登录
      wx.login({
        success: res => {
          // 发送 res.code 到后台换取 openId, sessionKey, unionId
          console.log('code', res.code)
          resolve(res.code)
        },
        fail: reject
      })
    })
  },
  async postUserInfo(userInfo) {
    const wxCode = await this.getWxLoginCode()

    console.log('wxCode=', wxCode)
    wx.request({
      url: 'http://tp6wechat.com:8383/api/passport/loginMpWx',
      method: 'POST',
      header: {
        'platform': 'MP-WEIXIN'
      },
      data: {
        form: {
          partyData: {
            code: wxCode,
            oauth: "MP-WEIXIN",
            userInfo: userInfo
          }
        }
      },
      success (res) {
        console.log(res.data)
        token = res.data.data.token
        wx.showToast({
          title: res.data.message,
          icon: 'none'
        })
      },
      fail (err) {
        console.log('err=', err)
      }
    })
  },
  getUserProfile(e) {
    // 推荐使用wx.getUserProfile获取用户信息，开发者每次通过该接口获取用户个人信息均需用户确认，开发者妥善保管用户快速填写的头像昵称，避免重复弹窗
    wx.getUserProfile({
      desc: '展示用户信息', // 声明获取用户个人信息后的用途，后续会展示在弹窗中，请谨慎填写
      success: (res) => {
        console.log(res)
        this.setData({
          userInfo: res.userInfo,
          hasUserInfo: true
        })
        this.postUserInfo(res.userInfo)
      }
    })
  },
  getUserProfileForMobileRegister(e) {
    wx.getUserProfile({
      desc: '展示用户信息', // 声明获取用户个人信息后的用途，后续会展示在弹窗中，请谨慎填写
      success: (res) => {
        console.log(res)
        this.setData({
          userInfo: res.userInfo,
          hasUserInfo: true
        })
      }
    })
  },
  getPhoneNumberBindAcc (e) {
    console.log(e.detail)
    if (e.detail.errMsg == 'getPhoneNumber:ok') {
      this.postUserBindMobile(e.detail)
    }
  },
  async postUserBindMobile(phoneInfo) {
    if (Object.keys(this.data.userInfo).length == 0) {
      wx.showToast({
        title: '请先授权用户信息',
        icon: 'none'
      })
      return
    }
    const wxCode = await this.getWxLoginCode()
    wx.request({
      url: 'http://tp6wechat.com:8383/api/user/bindMobile',
      method: 'POST',
      header: {
        'platform': 'MP-WEIXIN',
        'Access-Token': token
      },
      data: {
        form: {
          // mobile: '11111111',
          code: wxCode,
          isParty: true,
          encryptedData: phoneInfo.encryptedData,
          iv: phoneInfo.iv,
          partyData: {
            code: wxCode,
            oauth: "MP-WEIXIN",
            userInfo: this.data.userInfo
          }
        }
      },
      success (res) {
        console.log(res.data)
        // token = res.data.data.token
        wx.showToast({
          title: res.data.message,
          icon: 'none'
        })
      },
      fail (err) {
        console.log('err=', err)
      }
    })
  },
  getPhoneNumber (e) {
    if (e.detail.errMsg == 'getPhoneNumber:ok') {
      this.postUserMobile(e.detail)
    }
  },
  async postUserMobile(phoneInfo) {
    if (Object.keys(this.data.userInfo).length == 0) {
      wx.showToast({
        title: '请先授权用户信息',
        icon: 'none'
      })
      return
    }
    const wxCode = await this.getWxLoginCode()
    wx.request({
      url: 'http://tp6wechat.com:8383/api/passport/loginMpWxMobile',
      method: 'POST',
      header: {
        'platform': 'MP-WEIXIN'
      },
      data: {
        form: {
          code: wxCode,
          isParty: true,
          encryptedData: phoneInfo.encryptedData,
          iv: phoneInfo.iv,
          partyData: {
            code: wxCode,
            oauth: "MP-WEIXIN",
            userInfo: this.data.userInfo
          }
        }
      },
      success (res) {
        console.log(res.data)
        token = res.data.data.token
        wx.showToast({
          title: res.data.message,
          icon: 'none'
        })
      },
      fail (err) {
        console.log('err=', err)
      }
    })
  },
  getWxUserInfo(e) {
    wx.request({
      url: 'http://tp6wechat.com:8383/api/user/info',
      header: {
        'platform': 'MP-WEIXIN',
        'Access-Token': token
      },
      success (res) {
        console.log(res.data)
        wx.showToast({
          title: res.data.message,
          icon: 'none'
        })
      },
      fail (err) {
        console.log('err=', err)
      }
    })
  }
})
