import FilterRatingPlugin from 'src/plugin/listing/filter-rating.plugin';

export default class RtuxFilterRatingPlugin extends FilterRatingPlugin {
    reset(id) {
        if (id === this.options.name) {
            this.ratingSystem.resetRating();
        }
    }

    getLabels() {
        const labels = super.getLabels(...arguments);
        if (labels.length > 0) {
            labels[0].id = this.options.name;
        }
        return labels;
    }
}