import 'package:flutter/material.dart';
import 'package:fluttertoast/fluttertoast.dart';
import 'package:provider/provider.dart';
import '../../l10n/app_strings.dart';
import '../../providers/locale_provider.dart';
import '../../services/api_service.dart';
import '../../theme/app_theme.dart';
import '../../widgets/empty_state.dart';

class AddressesScreen extends StatefulWidget {
  const AddressesScreen({super.key, this.selectMode = false});
  final bool selectMode;

  @override
  State<AddressesScreen> createState() => _AddressesScreenState();
}

class _AddressesScreenState extends State<AddressesScreen> {
  List<Map<String, dynamic>> _addresses = [];
  bool _loading = true;

  @override
  void initState() { super.initState(); _load(); }

  Future<void> _load() async {
    try {
      final res = await ApiService.instance.get('/addresses');
      _addresses = ((res as Map)['addresses'] as List).cast<Map<String, dynamic>>();
    } catch (_) {}
    if (mounted) setState(() => _loading = false);
  }

  Future<void> _delete(int id, String deletedMsg, String errorMsg) async {
    try {
      await ApiService.instance.delete('/addresses/$id');
      _load();
      Fluttertoast.showToast(
          msg: deletedMsg, backgroundColor: AppColors.success, textColor: Colors.white);
    } catch (e) {
      Fluttertoast.showToast(
          msg: e.toString(), backgroundColor: AppColors.danger, textColor: Colors.white);
    }
  }

  @override
  Widget build(BuildContext context) {
    final s = context.watch<LocaleProvider>().s;

    return Scaffold(
      backgroundColor: AppColors.grayBg,
      appBar: AppBar(title: Text(s.myAddresses)),
      floatingActionButton: FloatingActionButton.extended(
        backgroundColor: AppColors.orange,
        onPressed: () async {
          final saved = await Navigator.of(context).push<bool>(
            MaterialPageRoute(builder: (_) => const AddressFormScreen()));
          if (saved == true) _load();
        },
        icon: const Icon(Icons.add_rounded, color: Colors.white),
        label: Text(s.newAddress,
            style: const TextStyle(fontFamily: 'Cairo', fontWeight: FontWeight.w800, color: Colors.white)),
      ),
      body: _loading
        ? const Center(child: CircularProgressIndicator(color: AppColors.orange))
        : _addresses.isEmpty
          ? EmptyState(
              icon: Icons.location_on_outlined,
              title: s.noAddresses,
              subtitle: s.noAddressesSub,
              ctaLabel: s.addNewAddress,
              onCta: () async {
                final saved = await Navigator.of(context).push<bool>(
                  MaterialPageRoute(builder: (_) => const AddressFormScreen()));
                if (saved == true) _load();
              },
            )
          : RefreshIndicator(
              color: AppColors.orange,
              onRefresh: _load,
              child: ListView.separated(
                padding: const EdgeInsets.fromLTRB(14, 14, 14, 100),
                itemCount: _addresses.length,
                separatorBuilder: (_, __) => const SizedBox(height: 10),
                itemBuilder: (_, i) {
                  final a = _addresses[i];
                  final isDefault = a['is_default'] == true;
                  return GestureDetector(
                    onTap: () {
                      if (widget.selectMode) Navigator.of(context).pop(a);
                    },
                    child: Container(
                      padding: const EdgeInsets.all(14),
                      decoration: BoxDecoration(
                        color: Colors.white,
                        borderRadius: BorderRadius.circular(AppRadius.lg),
                        border: isDefault
                          ? Border.all(color: AppColors.orange, width: 1.5)
                          : null,
                        boxShadow: AppShadows.card,
                      ),
                      child: Row(children: [
                        Container(
                          width: 46, height: 46,
                          decoration: BoxDecoration(
                            color: isDefault ? AppColors.orangeLight : AppColors.blueLight,
                            borderRadius: BorderRadius.circular(13),
                          ),
                          alignment: Alignment.center,
                          child: Text(
                            isDefault ? '🏠' : '📍',
                            style: const TextStyle(fontSize: 22),
                          ),
                        ),
                        const SizedBox(width: 12),
                        Expanded(child: Column(crossAxisAlignment: CrossAxisAlignment.start, children: [
                          Row(children: [
                            Text(
                              a['label']?.toString() ?? s.newAddress,
                              style: const TextStyle(
                                fontSize: 13, fontWeight: FontWeight.w900,
                                fontFamily: 'Cairo', color: AppColors.text,
                              ),
                            ),
                            if (isDefault) ...[
                              const SizedBox(width: 6),
                              Container(
                                padding: const EdgeInsets.symmetric(horizontal: 8, vertical: 2),
                                decoration: BoxDecoration(
                                    color: AppColors.orangeLight,
                                    borderRadius: BorderRadius.circular(6)),
                                child: Text(
                                  s.defaultTag,
                                  style: const TextStyle(
                                    color: AppColors.orange, fontSize: 9,
                                    fontWeight: FontWeight.w800, fontFamily: 'Cairo',
                                  ),
                                ),
                              ),
                            ],
                          ]),
                          const SizedBox(height: 4),
                          Text(
                            '${a['city'] ?? ''}${a['area'] != null ? ' • ${a['area']}' : ''}',
                            style: const TextStyle(
                              fontSize: 12, color: AppColors.gray, fontFamily: 'Cairo',
                            ),
                          ),
                          Text(
                            '${a['address_line'] ?? ''}',
                            maxLines: 1, overflow: TextOverflow.ellipsis,
                            style: const TextStyle(
                              fontSize: 12, color: AppColors.gray, fontFamily: 'Cairo',
                            ),
                          ),
                          if (a['phone'] != null) ...[
                            const SizedBox(height: 2),
                            Text(
                              '📞 ${a['phone']}',
                              style: const TextStyle(
                                fontSize: 11, color: AppColors.grayLight, fontFamily: 'Cairo',
                              ),
                            ),
                          ],
                        ])),
                        if (!widget.selectMode)
                          IconButton(
                            icon: const Icon(Icons.delete_outline_rounded,
                                color: AppColors.danger, size: 22),
                            onPressed: () async {
                              final confirm = await showDialog<bool>(
                                context: context,
                                builder: (_) => AlertDialog(
                                  title: Text(s.deleteAddressTitle,
                                      style: const TextStyle(
                                          fontFamily: 'Cairo', fontWeight: FontWeight.w900)),
                                  content: Text(s.deleteAddressConfirm,
                                      style: const TextStyle(fontFamily: 'Cairo')),
                                  actions: [
                                    TextButton(
                                      onPressed: () => Navigator.pop(context, false),
                                      child: Text(s.cancel,
                                          style: const TextStyle(fontFamily: 'Cairo')),
                                    ),
                                    TextButton(
                                      onPressed: () => Navigator.pop(context, true),
                                      child: Text(s.delete,
                                          style: const TextStyle(
                                              color: AppColors.danger, fontFamily: 'Cairo',
                                              fontWeight: FontWeight.w800)),
                                    ),
                                  ],
                                ),
                              );
                              if (confirm == true) {
                                _delete(a['id'] as int, s.deleted, s.error);
                              }
                            },
                          )
                        else
                          const Icon(Icons.chevron_left_rounded,
                              color: AppColors.border, size: 22),
                      ]),
                    ),
                  );
                },
              ),
            ),
    );
  }
}

// ─── Address form ──────────────────────────────────────────────────────────────
class AddressFormScreen extends StatefulWidget {
  const AddressFormScreen({super.key});

  @override
  State<AddressFormScreen> createState() => _AddressFormScreenState();
}

class _AddressFormScreenState extends State<AddressFormScreen> {
  final _form     = GlobalKey<FormState>();
  final _label    = TextEditingController();
  final _city     = TextEditingController(text: 'الخليل');
  final _area     = TextEditingController();
  final _address  = TextEditingController();
  final _building = TextEditingController();
  final _phone    = TextEditingController();
  final _notes    = TextEditingController();
  String _phonePrefix = '+970';
  bool   _isDefault   = false;
  bool   _saving      = false;
  List<Map<String, dynamic>> _zones = [];
  int?   _zoneId;

  @override
  void initState() {
    super.initState();
    _loadZones();
  }

  Future<void> _loadZones() async {
    try {
      final res = await ApiService.instance.get('/delivery-zones');
      setState(() => _zones =
          ((res as Map)['zones'] as List).cast<Map<String, dynamic>>());
    } catch (_) {}
  }

  Future<void> _save(S s) async {
    if (!_form.currentState!.validate()) return;
    if (_zoneId == null) {
      Fluttertoast.showToast(msg: s.selectZoneMsg);
      return;
    }
    setState(() => _saving = true);
    try {
      await ApiService.instance.post('/addresses', data: {
        'label':            _label.text.trim(),
        'delivery_zone_id': _zoneId,
        'city':             _city.text.trim(),
        'area':             _area.text.trim(),
        'address_line':     _address.text.trim(),
        'building':         _building.text.trim(),
        'phone':            _phonePrefix + _phone.text.replaceAll(RegExp(r'\D'), ''),
        'notes':            _notes.text.trim(),
        'is_default':       _isDefault,
      });
      if (!mounted) return;
      Navigator.of(context).pop(true);
      Fluttertoast.showToast(
          msg: s.saveAddressSuccess,
          backgroundColor: AppColors.success,
          textColor: Colors.white);
    } catch (e) {
      Fluttertoast.showToast(
          msg: e.toString(), backgroundColor: AppColors.danger, textColor: Colors.white);
    } finally {
      if (mounted) setState(() => _saving = false);
    }
  }

  @override
  Widget build(BuildContext context) {
    final s = context.watch<LocaleProvider>().s;

    return Scaffold(
      appBar: AppBar(title: Text(s.newAddress)),
      body: Form(
        key: _form,
        child: ListView(padding: const EdgeInsets.all(16), children: [
          // Label
          TextFormField(
            controller: _label,
            decoration: InputDecoration(
              labelText: s.addressLabel,
              hintText: s.addressNameHint,
            ),
          ),
          const SizedBox(height: 10),

          // Delivery zone
          DropdownButtonFormField<int>(
            value: _zoneId, isExpanded: true,
            decoration: InputDecoration(labelText: '${s.deliveryZone} *'),
            items: _zones.map((z) => DropdownMenuItem<int>(
              value: z['id'] as int,
              child: Text(
                '${z['name']} — ${(z['base_fee'] as num) > 0 ? '${z['base_fee']} ₪' : s.freeDelivery}',
                style: const TextStyle(fontFamily: 'Cairo'),
              ),
            )).toList(),
            onChanged: (v) => setState(() => _zoneId = v),
          ),
          const SizedBox(height: 10),

          // City
          TextFormField(
            controller: _city,
            decoration: InputDecoration(labelText: '${s.city} *'),
            validator: (v) => v == null || v.isEmpty ? s.required : null,
          ),
          const SizedBox(height: 10),

          // Address detail
          TextFormField(
            controller: _address,
            decoration: InputDecoration(labelText: '${s.addressDetail} *'),
            validator: (v) => v == null || v.isEmpty ? s.required : null,
          ),
          const SizedBox(height: 10),

          // Area + building
          Row(children: [
            Expanded(child: TextFormField(
                controller: _area,
                decoration: InputDecoration(labelText: s.neighborhood))),
            const SizedBox(width: 10),
            Expanded(child: TextFormField(
                controller: _building,
                decoration: InputDecoration(labelText: s.building))),
          ]),
          const SizedBox(height: 10),

          // Phone prefix + number
          Row(children: [
            Container(
              padding: const EdgeInsets.symmetric(horizontal: 10),
              decoration: BoxDecoration(
                border: Border.all(color: AppColors.border, width: 1.2),
                borderRadius: BorderRadius.circular(12),
                color: Colors.white,
              ),
              child: DropdownButton<String>(
                value: _phonePrefix, underline: const SizedBox.shrink(),
                items: const [
                  DropdownMenuItem(value: '+970',
                      child: Text('🇵🇸 +970', style: TextStyle(fontFamily: 'Cairo'))),
                  DropdownMenuItem(value: '+972',
                      child: Text('🇮🇱 +972', style: TextStyle(fontFamily: 'Cairo'))),
                ],
                onChanged: (v) => setState(() => _phonePrefix = v ?? '+970'),
              ),
            ),
            const SizedBox(width: 10),
            Expanded(child: TextFormField(
              controller: _phone,
              keyboardType: TextInputType.phone,
              decoration: InputDecoration(
                  labelText: '${s.phoneNumber} *', hintText: '5XX XXX XXX'),
              validator: (v) {
                final d = (v ?? '').replaceAll(RegExp(r'\D'), '');
                return (d.length < 8 || d.length > 10) ? s.error : null;
              },
            )),
          ]),
          const SizedBox(height: 10),

          // Notes
          TextFormField(
            controller: _notes, maxLines: 2,
            decoration: InputDecoration(labelText: s.notes),
          ),
          const SizedBox(height: 10),

          // Default toggle
          SwitchListTile(
            value: _isDefault,
            onChanged: (v) => setState(() => _isDefault = v),
            title: Text(s.makeDefault,
                style: const TextStyle(fontFamily: 'Cairo', fontWeight: FontWeight.w700)),
            activeColor: AppColors.orange,
          ),
          const SizedBox(height: 20),

          // Save button
          ElevatedButton(
            onPressed: _saving ? null : () => _save(s),
            child: _saving
              ? const SizedBox(width: 22, height: 22,
                  child: CircularProgressIndicator(strokeWidth: 2, color: Colors.white))
              : Text(s.saveAddress,
                  style: const TextStyle(fontFamily: 'Cairo', fontWeight: FontWeight.w800)),
          ),
        ]),
      ),
    );
  }
}
