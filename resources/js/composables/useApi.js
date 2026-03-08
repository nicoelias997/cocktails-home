import { ref } from 'vue'
import axios from 'axios'
 
export function useApi() {
  const loading = ref(false)
  const error   = ref(null)
  const request = async (method, url, data = null) => {
    loading.value = true; error.value = null
    try {
      return (await axios({ method, url, data })).data
    } catch (err) {
      error.value = err.response?.data?.message || 'Error'
      throw err
    } finally { loading.value = false }
  }
  return {
    loading, error,
    get:    (url)       => request('GET',    url),
    post:   (url, data) => request('POST',   url, data),
    put:    (url, data) => request('PUT',    url, data),
    delete: (url)       => request('DELETE', url),
  }
}

