# Admin Panel Release Governance Checklist (2026-03-02)

## 1. Scope Confirmation
- [ ] Değişen modüller net: `app/Filament`, `Modules/Landing`, `Modules/Leads`, `database/migrations`, `tests`.
- [ ] Kırıcı contract değişikliği var mı? (API/route/schema)
- [ ] Feature flag gerektiren değişiklikler açık mı kapalı mı?

## 2. Quality Gate
- [ ] `./scripts/run-quality-gate.ps1` başarılı.
- [ ] Kritik akışlar için hedefli testler başarılı:
  - [ ] Admin page authorization
  - [ ] Ops widgets
  - [ ] Marketing widgets
  - [ ] Form submission triage workflow

## 3. Security and Access
- [ ] `ManageSettings` yalnızca `settings.manage` yetkisi ile erişilebilir.
- [ ] Role-policy matrix güncel.
- [ ] Audit log görünürlüğü sadece uygun rollerde.
- [ ] Bulk action rate limit aktif ve doğrulandı.

## 4. Operational Readiness
- [ ] Dashboard widget polling yükü kabul edilebilir.
- [ ] SLA alert eşikleri doğrulandı (lead/ticket/order).
- [ ] Form triage alanları (`assigned_to`, `follow_up_at`, `internal_note`) migration sonrası doğrulandı.

## 5. Marketing and SEO
- [ ] Funnel widget metrikleri doğrulandı.
- [ ] Source quality widget verisi doğru.
- [ ] SEO health widget metrikleri doğru.
- [ ] Landing SEO durum badge ile widget sonuçları tutarlı.

## 6. Rollback Plan
- [ ] Migration rollback sırası hazır.
- [ ] Yeni widget/resource devre dışı bırakma planı hazır.
- [ ] Son stabil sürüme dönüş adımları net.

## 7. Go/No-Go Decision
- [ ] P0/P1 açık hata yok.
- [ ] Testler yeşil.
- [ ] Rollback hazır.
- [ ] Operasyon ve ürün onayı alındı.
