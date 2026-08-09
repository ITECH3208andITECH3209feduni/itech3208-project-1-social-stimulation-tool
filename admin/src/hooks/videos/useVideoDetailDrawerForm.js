import { useState, useEffect } from "react";

const useVideoDetailDrawerForm = (video, isOpen, onSave) => {
    const [formData, setFormData] = useState({
        title: "",
        description: "",
        categoryId: "",
        subCategoryId: "",
        video: null,
    });

    // Pre-populate form when the drawer opens with an existing video
    useEffect(() => {
        if (isOpen && video) {
            setFormData({
                title: video.title || "",
                description: video.description || "",
                categoryId: video.category?.id || video.categoryId || "",
                subCategoryId: video.subCategory?.id || video.subCategoryId || "",
                video: null, // Initial file is null until user selects a new one
            });
        } else if (!isOpen) {
            setFormData({
                title: "",
                description: "",
                categoryId: "",
                subCategoryId: "",
                video: null,
            });
        }
    }, [isOpen, video]);

    const handleInputChange = (key, value) => {
        setFormData((prev) => ({ ...prev, [key]: value }));
    };

    const handleCategoryChange = (e) => {
        // Reset subCategoryId whenever the parent category changes
        setFormData((prev) => ({ ...prev, categoryId: e.target.value, subCategoryId: "" }));
    };

    const handleSave = () => {
        const payload = new FormData();

        if (formData.title !== video?.title) payload.append("title", formData.title);
        if (formData.description !== video?.description) payload.append("description", formData.description);

        const originalCategoryId = video?.category?.id || video?.categoryId || "";
        const originalSubCategoryId = video?.subCategory?.id || video?.subCategoryId || "";

        if (formData.categoryId !== originalCategoryId) payload.append("categoryId", formData.categoryId);
        if (formData.subCategoryId !== originalSubCategoryId) payload.append("subCategoryId", formData.subCategoryId);

        if (formData.video) payload.append("video", formData.video);

        onSave(video.id, payload);
    };

    return {
        formData,
        handleInputChange,
        handleCategoryChange,
        handleSave,
    };
};

export default useVideoDetailDrawerForm;

