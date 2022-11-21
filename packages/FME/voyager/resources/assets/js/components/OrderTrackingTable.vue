<template>
    <div>
        <div class="px-2">
            <h1 class="h5 mb-3 font-weight-bold">Tracking</h1>
            <table class="table table-sm table-striped table-hover mb-0">
                <thead class="thead-dark">
                    <tr>
                        <th>Carrier</th>
                        <th>Number</th>
                        <th>Created at</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="tracking in trackingNumbers">
                        <td>
                            <span v-if="tracking.carrier_name !== null">{{ tracking.carrier_name }}</span>
                            <span v-else>---</span>
                        </td>
                        <td>
                            <span v-if="tracking.number !== null">{{ tracking.number }}</span>
                            <span v-else>---</span>
                        </td>
                        <td>{{ tracking.created_at | usDate }}</td>
                        <td class="text-right">
                            <div class="btn-group">
                                <a v-if="tracking.file_name !== null && tracking.file_name !== undefined" class="btn btn-sm btn-pink view d-flex align-items-center" target="_blank" :href="storagePath + '/' + tracking.file_name">
                                    <i class="fas fa-file-download"></i>
                                </a>
                                <button v-else @click.prevent disabled="true" class="btn btn-sm btn-pink view d-flex align-items-center" style="opacity: 0.2;">
                                    <i class="fas fa-file-download"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</template>

<script>
    export default {
        props: [
            'trackingNumbers', 'storagePath'
        ],
    }
</script>