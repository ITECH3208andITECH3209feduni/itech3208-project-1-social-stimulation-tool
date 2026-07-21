import { useState, useEffect } from "react";
import { buildVideoFormData } from "@/utils/buildVideoFormData";

const useVideoDetailDrawerForm = (video, isOpen, onSave) => {
    const [formData, setFormData] = useState({
        title: "",
        description: "",
        video: null,
    });

    useEffect(() => {
        if (isOpen && video) {
            setFormData({
                title: video.title || "",
                description: video.description || "",
                video: null, // Initial file is null until user selects a new one
            });
        } else if (!isOpen) {
            setFormData({
                title: "",
                description: "",
                video: null,
            });
        }
    }, [isOpen, video]);

    const handleInputChange = (key, value) => {
        setFormData((prev) => ({ ...prev, [key]: value }));
    };

    const handleSave = () => {
        // Build FormData properly since we have file upload
        const payload = new FormData();
        if (formData.title !== video?.title) payload.append("title", formData.title);
        if (formData.description !== video?.description) payload.append("description", formData.description);
        if (formData.video) payload.append("video", formData.video);

        onSave(video.id, payload);
    };

    return {
        formData,
        handleInputChange,
        handleSave,
    };
};

export default useVideoDetailDrawerForm;
