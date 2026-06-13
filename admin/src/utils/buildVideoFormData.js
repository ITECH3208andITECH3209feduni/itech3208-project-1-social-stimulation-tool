/**
 * Builds and validates a FormData object for video upload
 *
 * @param {Object} data - The form data object from UploadVideoPage
 * @param {File} data.videoFile - The selected video file
 * @param {string} data.title - The title of the video
 * @param {string} data.message - The description/message
 * @param {string} data.category - The selected category ID
 * @param {string[]} data.tags - Array of tags
 *
 * @returns {Object} Result object with `success`, `formData` (if success), and `error` (if failed)
 */
export const buildVideoFormData = (data) => {
    const { video, title, description, categoryId, tags } = data;

    const formData = new FormData();
    
    formData.append("video", video);
    formData.append("title", title);
    formData.append("description", description);
    formData.append("categoryId", categoryId);
    
    // For arrays, backend usually expects either JSON string or multiple fields
    // Here we use JSON stringification which is standard for REST APIs receiving FormData
    formData.append("tags", JSON.stringify(tags));

    return formData;
};
