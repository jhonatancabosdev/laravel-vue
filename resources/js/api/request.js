import axios from 'axios';

const client = axios.create({
  baseURL: '/',
});

export default {
    all(path, params) {
        return client.get(path, params);
    },
    find(path, id) {
        return client.get(`${path}/${id}`);
    },
    add(path, data){
        return client.post(`${path}/`, data);
    },
    update(path, id, data) {
        if (data !== undefined && id !== undefined) {
            return client.put(`${path}/${id}`, data);
        }
        return client.put(`${path}`, id);
    },
    delete(path, id) {
        return client.delete(`${path}/${id}`);
    },
};