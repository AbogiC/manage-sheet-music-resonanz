<template>
  <div class="auth-container">
    <div class="auth-card">
      <div class="auth-header">
        <h1>Create Account</h1>
        <p>Join us to organize your sheet music</p>
      </div>

      <form @submit.prevent="handleRegister" class="auth-form">
        <div class="form-group">
          <label for="name">Full Name</label>
          <input
            id="name"
            v-model="form.name"
            type="text"
            required
            placeholder="Enter your full name"
          />
        </div>

        <div class="form-group">
          <label for="email">Email</label>
          <input
            id="email"
            v-model="form.email"
            type="email"
            required
            placeholder="Enter your email"
          />
        </div>

        <div class="form-group">
          <label for="password">Password</label>
          <input
            id="password"
            v-model="form.password"
            type="password"
            required
            placeholder="Create a password"
            minlength="8"
          />
        </div>

        <div class="form-group">
          <label for="password_confirmation">Confirm Password</label>
          <input
            id="password_confirmation"
            v-model="form.password_confirmation"
            type="password"
            required
            placeholder="Confirm your password"
            minlength="8"
          />
        </div>

        <button type="submit" class="auth-btn" :disabled="loading">
          {{ loading ? "Creating account..." : "Create Account" }}
        </button>
      </form>

      <div class="auth-footer">
        <p>
          Already have an account?
          <router-link to="/login" class="auth-link">Sign in</router-link>
        </p>
      </div>
    </div>
  </div>
</template>

<script>
import { ref } from "vue";
import { useRouter } from "vue-router";
import api from "../services/api";

export default {
  name: "Register",
  setup() {
    const router = useRouter();
    const loading = ref(false);

    const form = ref({
      name: "",
      email: "",
      password: "",
      password_confirmation: "",
    });

    const handleRegister = async () => {
      if (form.value.password !== form.value.password_confirmation) {
        alert("Passwords do not match");
        return;
      }

      loading.value = true;
      try {
        const response = await api.register(form.value);

        // Store token and user data
        localStorage.setItem("token", response.data.token);
        localStorage.setItem("user", JSON.stringify(response.data.user));

        // Redirect to home
        router.push("/");
      } catch (error) {
        console.error("Registration error:", error);
        let errorMessage = "Registration failed. Please try again.";

        if (error.response?.status === 422 && error.response.data.errors) {
          const errors = Object.values(error.response.data.errors).flat();
          errorMessage = errors.join("\n");
        } else if (error.response?.data?.message) {
          errorMessage = error.response.data.message;
        }

        alert(errorMessage);
      } finally {
        loading.value = false;
      }
    };

    return {
      form,
      loading,
      handleRegister,
    };
  },
};
</script>

<style scoped>
.auth-container {
  min-height: 100vh;
  display: flex;
  align-items: center;
  justify-content: center;
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  padding: 20px;
}

.auth-card {
  background: white;
  border-radius: 15px;
  padding: 40px;
  box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1);
  width: 100%;
  max-width: 400px;
  animation: slideIn 0.8s ease-out;
}

@keyframes slideIn {
  from {
    opacity: 0;
    transform: translateY(50px) scale(0.9);
  }
  to {
    opacity: 1;
    transform: translateY(0) scale(1);
  }
}

.auth-header {
  text-align: center;
  margin-bottom: 30px;
}

.auth-header h1 {
  color: #2c3e50;
  margin: 0 0 10px 0;
  font-size: 2rem;
  font-weight: 600;
  animation: fadeInUp 0.8s ease-out 0.2s both;
}

.auth-logo {
  width: 80px;
  height: 80px;
  margin-bottom: 20px;
  animation: bounceIn 0.8s ease-out;
}

@keyframes fadeInUp {
  from {
    opacity: 0;
    transform: translateY(30px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

@keyframes bounceIn {
  0% {
    opacity: 0;
    transform: scale(0.3);
  }
  50% {
    opacity: 1;
    transform: scale(1.05);
  }
  70% {
    transform: scale(0.9);
  }
  100% {
    opacity: 1;
    transform: scale(1);
  }
}

.auth-header p {
  color: #7f8c8d;
  margin: 0;
  font-size: 1rem;
}

.auth-form {
  margin-bottom: 30px;
}

.form-group {
  margin-bottom: 20px;
}

.form-group label {
  display: block;
  margin-bottom: 8px;
  color: #4a6491;
  font-weight: 500;
}

.form-group input {
  width: 100%;
  padding: 12px 15px;
  border: 2px solid #e1e7f0;
  border-radius: 8px;
  font-size: 1rem;
  transition: all 0.3s ease;
  background: linear-gradient(135deg, #ffffff 0%, #f8f9fa 100%);
}

.form-group input:focus {
  outline: none;
  border-color: #4a6491;
  box-shadow: 0 0 0 3px rgba(74, 100, 145, 0.1);
  transform: translateY(-2px);
  background: white;
}

.auth-btn {
  width: 100%;
  padding: 14px;
  background: #4a6491;
  color: white;
  border: none;
  border-radius: 8px;
  font-size: 1rem;
  font-weight: 600;
  cursor: pointer;
  transition: background 0.3s;
}

.auth-btn:hover:not(:disabled) {
  background: #3a5479;
}

.auth-btn:disabled {
  background: #bdc3c7;
  cursor: not-allowed;
}

.auth-footer {
  text-align: center;
  padding-top: 20px;
  border-top: 1px solid #e1e7f0;
}

.auth-footer p {
  margin: 0;
  color: #7f8c8d;
}

.auth-link {
  color: #4a6491;
  text-decoration: none;
  font-weight: 600;
}

.auth-link:hover {
  text-decoration: underline;
}
</style>
