import 'dart:typed_data';

import 'package:file_picker/file_picker.dart';
import 'package:flutter/foundation.dart' show kIsWeb;
import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'package:go_router/go_router.dart';
import 'package:image_picker/image_picker.dart';
import '../../../core/theme/app_theme.dart';
import '../../../core/widgets/app_button.dart';
import '../../../core/widgets/app_text_field.dart';
import '../../competitions/providers/competition_provider.dart';
import '../providers/submission_provider.dart';

class SubmitScreen extends ConsumerStatefulWidget {
  final int competitionId;

  const SubmitScreen({super.key, required this.competitionId});

  @override
  ConsumerState<SubmitScreen> createState() => _SubmitScreenState();
}

class _SubmitScreenState extends ConsumerState<SubmitScreen> {
  final _textController = TextEditingController();
  final _linkController = TextEditingController();
  Uint8List? _fileBytes;
  String? _fileName;
  String? _submissionKind;

  @override
  void dispose() {
    _textController.dispose();
    _linkController.dispose();
    super.dispose();
  }

  Future<void> _pickImage() async {
    if (kIsWeb) {
      final result = await FilePicker.platform.pickFiles(
        type: FileType.image,
        withData: true,
      );
      if (result != null && result.files.single.bytes != null) {
        setState(() {
          _fileBytes = result.files.single.bytes;
          _fileName = result.files.single.name;
        });
      }
    } else {
      final picker = ImagePicker();
      final image = await picker.pickImage(source: ImageSource.gallery, imageQuality: 85);
      if (image != null) {
        final bytes = await image.readAsBytes();
        setState(() {
          _fileBytes = Uint8List.fromList(bytes);
          _fileName = image.name;
        });
      }
    }
  }

  Future<void> _pickFile() async {
    final result = await FilePicker.platform.pickFiles(
      type: FileType.custom,
      allowedExtensions: ['pdf'],
      withData: true,
    );
    if (result != null && result.files.single.bytes != null) {
      setState(() {
        _fileBytes = result.files.single.bytes;
        _fileName = result.files.single.name;
      });
    }
  }

  Future<void> _submit() async {
    final kind = _submissionKind;
    if (kind == null) return;

    if (kind == 'text' && _textController.text.trim().isEmpty) {
      ScaffoldMessenger.of(context).showSnackBar(
        const SnackBar(content: Text('الرجاء كتابة النص أولاً')),
      );
      return;
    }
    if (kind == 'link' && _linkController.text.trim().isEmpty) {
      ScaffoldMessenger.of(context).showSnackBar(
        const SnackBar(content: Text('الرجاء إدخال الرابط أولاً')),
      );
      return;
    }
    if ((kind == 'image' || kind == 'pdf') && _fileBytes == null) {
      ScaffoldMessenger.of(context).showSnackBar(
        const SnackBar(content: Text('الرجاء اختيار الملف أولاً')),
      );
      return;
    }

    final success = await ref.read(submissionCreateProvider.notifier).submit(
          competitionId: widget.competitionId,
          textContent: kind == 'text' ? _textController.text.trim() : null,
          linkUrl: kind == 'link' ? _linkController.text.trim() : null,
          fileBytes: _fileBytes,
          fileName: _fileName,
        );

    if (success && mounted) {
      ScaffoldMessenger.of(context).showSnackBar(
        const SnackBar(
          content: Text('تم إرسال المشاركة بنجاح'),
          backgroundColor: AppColors.success,
        ),
      );
      ref.read(submissionListProvider.notifier).load();
      context.go('/my-submissions');
    }
  }

  @override
  Widget build(BuildContext context) {
    final competitionAsync = ref.watch(competitionDetailProvider(widget.competitionId));
    final createstate = ref.watch(submissionCreateProvider);

    return Scaffold(
      appBar: AppBar(title: const Text('مشاركة جديدة')),
      body: competitionAsync.when(
        loading: () => const Center(child: CircularProgressIndicator()),
        error: (e, _) => Center(child: Text('حدث خطأ: $e')),
        data: (competition) {
          _submissionKind = competition.competitionType?.submissionKind;
          final kind = _submissionKind!;

          return ListView(
            padding: const EdgeInsets.all(16),
            children: [
              Card(
                child: Padding(
                  padding: const EdgeInsets.all(16),
                  child: Column(
                    crossAxisAlignment: CrossAxisAlignment.start,
                    children: [
                      Text(
                        competition.title,
                        style: const TextStyle(
                          fontSize: 18,
                          fontWeight: FontWeight.bold,
                        ),
                      ),
                      const SizedBox(height: 8),
                      Text(
                        'نوع المشاركة: ${_kindLabel(kind)}',
                        style: const TextStyle(color: AppColors.textSecondary),
                      ),
                    ],
                  ),
                ),
              ),
              const SizedBox(height: 24),
              if (kind == 'text') ...[
                AppTextField(
                  label: 'نص المشاركة',
                  hint: 'اكتب مشاركتك هنا...',
                  controller: _textController,
                  maxLines: 8,
                  validator: (v) {
                    if (v == null || v.trim().isEmpty) return 'النص مطلوب';
                    return null;
                  },
                ),
              ] else if (kind == 'link') ...[
                AppTextField(
                  label: 'الرابط',
                  hint: 'https://...',
                  controller: _linkController,
                  keyboardType: TextInputType.url,
                  validator: (v) {
                    if (v == null || v.trim().isEmpty) return 'الرابط مطلوب';
                    if (!Uri.tryParse(v)!.hasAbsolutePath) return 'رابط غير صحيح';
                    return null;
                  },
                ),
              ] else if (kind == 'image') ...[
                _filePicker(
                  icon: Icons.image_outlined,
                  label: 'اختر صورة',
                  onTap: _pickImage,
                ),
              ] else if (kind == 'pdf') ...[
                _filePicker(
                  icon: Icons.picture_as_pdf_outlined,
                  label: 'اختر ملف PDF',
                  onTap: _pickFile,
                ),
              ] else if (kind == 'none') ...[
                Card(
                  child: Padding(
                    padding: const EdgeInsets.all(16),
                    child: Column(
                      children: [
                        const Icon(Icons.how_to_reg, size: 48, color: AppColors.primary),
                        const SizedBox(height: 12),
                        Text(
                          'هذه المسابقة تتطلب التسجيل فقط. اضغط على زر "تسجيل" للمشاركة.',
                          textAlign: TextAlign.center,
                          style: const TextStyle(color: AppColors.textSecondary),
                        ),
                      ],
                    ),
                  ),
                ),
              ],
              if (createstate.error != null) ...[
                const SizedBox(height: 16),
                Text(
                  createstate.error!,
                  textAlign: TextAlign.center,
                  style: const TextStyle(color: AppColors.error),
                ),
              ],
              const SizedBox(height: 24),
              if (createstate.status == SubmissionCreateStatus.uploading)
                Column(
                  children: [
                    LinearProgressIndicator(value: createstate.progress),
                    const SizedBox(height: 8),
                    Text(
                      'جاري الإرسال... ${(createstate.progress * 100).toInt()}%',
                      style: const TextStyle(color: AppColors.textSecondary),
                    ),
                  ],
                )
              else
                AppButton(
                  label: kind == 'none' ? 'تسجيل' : 'إرسال المشاركة',
                  icon: kind == 'none' ? Icons.how_to_reg : Icons.send,
                  onPressed: _submit,
                ),
            ],
          );
        },
      ),
    );
  }

  Widget _filePicker({
    required IconData icon,
    required String label,
    required VoidCallback onTap,
  }) {
    return InkWell(
      onTap: onTap,
      borderRadius: BorderRadius.circular(12),
      child: Container(
        padding: const EdgeInsets.all(24),
        decoration: BoxDecoration(
          border: Border.all(color: AppColors.border),
          borderRadius: BorderRadius.circular(12),
          color: AppColors.background,
        ),
        child: Column(
          children: [
            Icon(icon, size: 48, color: AppColors.primary),
            const SizedBox(height: 12),
            Text(label, style: const TextStyle(fontWeight: FontWeight.w600)),
            if (_fileName != null) ...[
              const SizedBox(height: 8),
              Container(
                padding: const EdgeInsets.symmetric(horizontal: 12, vertical: 6),
                decoration: BoxDecoration(
                  color: AppColors.primaryLight,
                  borderRadius: BorderRadius.circular(8),
                ),
                child: Text(
                  _fileName!,
                  style: const TextStyle(color: AppColors.primary, fontSize: 13),
                ),
              ),
            ],
          ],
        ),
      ),
    );
  }

  String _kindLabel(String kind) {
    switch (kind) {
      case 'image':
        return 'صورة';
      case 'pdf':
        return 'ملف PDF';
      case 'text':
        return 'نص';
      case 'link':
        return 'رابط';
      case 'none':
        return 'تسجيل فقط';
      default:
        return kind;
    }
  }
}
