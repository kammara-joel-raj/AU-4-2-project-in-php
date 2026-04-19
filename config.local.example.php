<?php

// Copy this file to config.local.php and replace the placeholder values.
// These values are loaded before includes/config.php reads the environment.

putenv('APP_BASE_URL=http://localhost/AUproj4%20harika/AU-4-2-project');
putenv('RAZORPAY_KEY_ID=rzp_test_your_key_id');
putenv('RAZORPAY_KEY_SECRET=your_razorpay_key_secret');
putenv('RAZORPAY_WEBHOOK_SECRET=your_razorpay_webhook_secret');

putenv('AI_PROVIDER=replicate');
putenv('AI_API_TOKEN=r8_your_replicate_token');
putenv('AI_MODEL_VERSION=cuuupid/idm-vton:0513734a452173b8173e907e3a59d19a36266e55b48528559432bd21c7d7e985');

putenv('UPLOAD_CLEANUP_LIMIT=30');
putenv('AI_MAX_IMAGE_BYTES=850000');
putenv('AI_MAX_EDGE=1280');
