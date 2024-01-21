<template>
  <div class="home">
    <h1>URL安全性检测</h1>
    <div class="bordered-container">
      <div class="input-group">
          <el-input v-model="inputValue" placeholder="请输入内容">
          <template slot="prepend">请输入链接：</template>
          </el-input>
          <el-button type="primary" plain @click="checkInput">查询</el-button>
      </div>
      <div class="disclaimer">本项目查询结果仅供参考，请自行研判风险。</div>
    </div>
    <div class="bordered-container2">
      <div class="column">
        <el-progress type="circle" :percentage="healthPercentage" text-inside :width="100"></el-progress>
        基础健康情况
      </div>
      <div class="column">
        <el-progress type="circle" :percentage="trustPercentage" text-inside :width="100"></el-progress>
        网址可信任度
      </div>
      <div class="column">
        <el-progress type="circle" :percentage="bwPercentage" text-inside :width="100"></el-progress>
        网址黑白程度
      </div>
      <div class="column">
        <el-progress type="circle" :percentage="reportCount" text-inside :width="100"></el-progress>
        用户举报次数
      </div>
    </div>
    <div class="bordered-container3">
    <div class="rating-container">
      网址评分：
      <el-rate v-model="value" disabled></el-rate>
    </div>
  </div>
  </div>
</template>

<script>
import { Input, Button, Progress, Rate } from 'element-ui';
import axios from 'axios';

export default {
  name: 'Custom-View',
  components: {
    'el-input': Input,
    'el-button': Button,
    'el-progress': Progress,
    'el-rate': Rate
  },
  data() {
    return {
      inputValue: '',
      healthPercentage: 0,
      trustPercentage: 0,
      bwPercentage: 0,
      reportCount: 0,
      // ...
    };
  },
  methods: {
    async checkInput() {
      if (!this.inputValue) {
        this.$message({
          message: '请输入链接',
          type: 'warning'
        });
      } else {
        try {
          const response = await axios.get(`/api/regcheck.php?domain=${this.inputValue}`);
          if (response.data === 1) {
            // 域名已注册，获取健康度数据
            const healthResponse = await axios.get(`/api/health.php?domain=${this.inputValue}`);
            this.healthPercentage = healthResponse.data;

            const trustResponse = await axios.get(`/api/domaintrust.php?domain=${this.inputValue}`);
            this.trustPercentage = trustResponse.data;

            const bwResponse = await axios.get(`/api/trust.php?domain=${this.inputValue}`);
            this.bwPercentage = bwResponse.data;

            const reportResponse = await axios.get(`/api/bancheck.php?domain=${this.inputValue}`);
            this.reportCount = reportResponse.data;

            // 计算 el-raye 值
            let newElRaye = (this.healthPercentage + this.trustPercentage + this.bwPercentage) / 3 - this.reportCount / 20;
            if (newElRaye < 0) {
              this.value = 0;
            } else if (newElRaye > 5) {
              this.value = 5;
            } else {
              this.value = newElRaye;
            }

          } else {
            this.$message({
              message: '域名未注册',
              type: 'warning'
            });
          }
        } catch (error) {
          console.error(error);
          this.$message({
            message: '查询失败',
            type: 'error'
          });
        }
      }
    },
  }
}
</script>

 
 <style>
 .home {
  font-size: 20px;
  text-align: center;
  margin-top: 25px;
 }
 
 .disclaimer {
  margin-top: 10px;
  font-size: 14px;
  color: #666;
 }
 
 h1 {
  color: white;
 }
 
 .bordered-container {
  background-color: white;
  border-radius: 10px;
  box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.5);
  height: 60px;
  margin: 20px 150px;
  opacity: 0.9;
  padding: 20px;
 }
 
 .bordered-container2 {
  background-color: white;
  border-radius: 10px;
  box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.5);
  height: 130px;
  margin: 20px 150px;
  opacity: 0.9;
  padding: 20px;
  display: flex; /* 添加这一行 */
  justify-content: space-between;
  flex-wrap: wrap;
}

 .bordered-container3 {
  background-color: white;
  border-radius: 10px;
  box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.5);
  height: 50px;
  margin: 20px 150px;
  opacity: 0.9;
  padding: 20px;
  display: flex;
  justify-content: space-between;
 }
 
 .column {
  width: 25%;
  display: flex;
  flex-direction: column;
  align-items: center;
 }
 
 .input-group {
  display: flex;
  align-items: center;
  gap: 10px;
 }
 
 .rating-container {
  display: flex;
  justify-content: flex-start;
  align-items: center;
}
 
@media only screen and (max-width: 600px) {
  .home {
    font-size: 16px;
    margin-top: 15px;
  }
 
  .bordered-container {
    height: 100px;
    margin: 8px 0px;
  }
 
  .bordered-container2 {
    height: 250px;
    margin: 8px 0px;
    flex-wrap: wrap; /* 添加这一行 */
  }
 
  .column {
    width: 50%; /* 修改这一行 */
    display: flex;
    flex-direction: column;
    align-items: center;
  }
 
  .input-group {
    flex-direction: column;
  }
  .bordered-container3 {
    height: 30px;
    margin: 8px 0px;
  }
}
 </style>
 